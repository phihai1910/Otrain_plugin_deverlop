select distinct
    fullname as "Course Name",
    firstname as "First name",
    lastname as "Last name",
    concat(retraining_notification_type, ' notice at ', retraining_notification_days, ' days') as "Next notification to send",
    next_retraining_date as "Date retraining needs to commence",
    days_to_next_retraining as "Days until Retraining Required"
from (
    select
        u.firstname,
        u.lastname,
        e.courseid,
        c.fullname,
        cc.retraining_notification_type,
        cc.retraining_notification_days,
        from_unixtime(comp.timecompleted) as timecompleted_human,
        from_unixtime(comp.timecompleted + (cc.retraining_frequency * 86400)) as next_retraining_date,
        round(
            ( (comp.timecompleted + (cc.retraining_frequency * 86400)) - unix_timestamp(curdate()) )
            / 86400
        ) as days_to_next_retraining
    from 
        prefix_enrol e inner join prefix_user_enrolments ue
        on e.id = ue.enrolid
        inner join prefix_user u
        on u.id = ue.userid
        inner join prefix_course c
        on c.id = e.courseid
        inner join prefix_course_custom cc
        on cc.courseid = e.courseid
        and cc.retraining_required = 1
        inner join prefix_course_completions comp
        on comp.course = e.courseid
        and comp.userid = ue.userid 
    where 
       comp.timecompleted is not null
        and not exists (
            select 1
            from prefix_retraining_notifications rn
            where rn.userid = ue.userid
            and rn.courseid = e.courseid
            and rn.userenrolmentid = ue.id
            and rn.notification_type = cc.retraining_notification_type
        )
        %%FILTER_COURSES:c.id%%
        %%FILTER_SEARCHTEXT:concat(trim(u.firstname), ' ', trim(u.lastname)):~%%
    ) retraining
where 
    retraining.days_to_next_retraining <= retraining_notification_days
order by
    fullname, days_to_next_retraining