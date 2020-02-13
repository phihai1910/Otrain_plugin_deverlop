select
	aic.userid,
	u.firstname,
	u.lastname,
	aic.code,
	from_unixtime(aic.timecreated) as issue_date,
	c.fullname,
	ce.name
from
	prefix_archive_issued_certs aic inner join prefix_user u
	on u.id = aic.userid
	inner join prefix_simplecertificate ce
	on ce.id = aic.certificateid
	inner join prefix_course c
	on c.id = ce.course