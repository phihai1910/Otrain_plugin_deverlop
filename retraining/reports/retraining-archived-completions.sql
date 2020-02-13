select
	c.fullname as "Course Name",
	acc.userid as "User ID",
	u.firstname as "First Name",
	u.lastname as "Last Name",
	from_unixtime(acc.timeenrolled) as "Enrolled On",
	from_unixtime(acc.timecompleted) as "Completed On"
from 
	prefix_archive_crse_comp acc inner join prefix_course c
	on acc.courseid = c.id
	inner join prefix_user u
	on u.id = acc.userid
where
	%%FILTER_COURSES:c.id%%
    %%FILTER_SEARCHTEXT:concat(trim(u.firstname), ' ', trim(u.lastname)):~%%