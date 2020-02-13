select
	c.fullname,
	u.firstname,
	u.lastname,
	rn.notification_type,
	from_unixtime(rn.notification_timestamp) as notification_sent,
	rn.notification_days	
from 
	prefix_retraining_notifications rn inner join prefix_course c
	on c.id = rn.courseid
	inner join prefix_user u
	on u.id = rn.userid
where
	1=1
	%%FILTER_COURSES:c.id%%
    %%FILTER_SEARCHTEXT:concat(trim(u.firstname), ' ', trim(u.lastname)):~%%
	%%FILTER_STARTTIME:rn.notification_timestamp:>%% 
	%%FILTER_ENDTIME:rn.notification_timestamp:<%%