SELECT f.id, f.name, t.count, t.worked, t.dstart, t.runtime, t.test_result FROM soap_function f
LEFT JOIN (
	SELECT 
		`function_id` AS `fid`, 
		COUNT(`id`) AS `count`, 
		SUM(CASE `status` WHEN 1 THEN 1 ELSE 0 END) AS `worked`,  
		SUM(CASE `status` WHEN 1 THEN (date_end-date_start) ELSE 0 END) AS `runtime`,
		MIN(`date_start`) AS `dstart`,
		MAX(`test_result`) AS `test_result`
	FROM soap_tests
	GROUP BY `function_id`
) t ON f.id=t.fid
WHERE f.service_id=4;

SELECT * FROM soap_function WHERE f.service_id=4;

//CASE `status` WHEN 1 OR 0 THEN 'ssss' ELSE 'xxx' END

SELECT COUNT(*) AS c FROM soap_tests WHERE service_id=4 AND status=1;
