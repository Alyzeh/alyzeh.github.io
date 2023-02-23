DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `ad_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ad_name` varchar(250) NOT NULL DEFAULT '',
  `ad_email` varchar(250) NOT NULL DEFAULT '',
  `password`  varchar(250) NOT NULL DEFAULT '',
  `status` varchar(250) NOT NULL DEFAULT '',
   PRIMARY KEY (`ad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

##########################
# Populate customers table
##########################
INSERT INTO admin(ad_id, ad_name, ad_email, password, status)
VALUES(10001, 'Scott Shaper', 'email@example.com', 'password', 'Admin');
VALUES(10002, 'Adrian Routhier', '123@example.com', 'password', 'Staff');