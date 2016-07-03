DELIMITER $$
CREATE PROCEDURE insert_user(
 IN firstname VARCHAR(50),
 IN lastname VARCHAR(50),
 IN address VARCHAR(50),
 IN number INT(11),
 IN cityname VARCHAR(50)
 IN Phone VARCHAR(50))
BEGIN
SELECT ID INTO @cityid
FROM city
WHERE `Name` = cityname
LIMIT 1;

INSERT INTO `user`(FirstName,LastName,Address,Number,CityID,Phone)
VALUES (firstname,lastname,address,number,@cityid,phone);

END$$
DELIMITER ;