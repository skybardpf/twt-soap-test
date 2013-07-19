/*
Navicat SQLite Data Transfer

Source Server         : SOAP-Unit-Tests
Source Server Version : 30706
Source Host           : :0

Target Server Type    : SQLite
Target Server Version : 30706
File Encoding         : 65001

Date: 2013-01-14 10:32:36
*/

PRAGMA foreign_keys = OFF;

-- ----------------------------
-- Table structure for "main"."soap_function"
-- ----------------------------
DROP TABLE "main"."soap_function";
CREATE TABLE "soap_function" (
"id"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"service_id"  INTEGER NOT NULL,
"name"  TEXT NOT NULL,
CONSTRAINT "fkey0" FOREIGN KEY ("service_id") REFERENCES "soap_service" ("id") ON DELETE RESTRICT ON UPDATE RESTRICT,
CONSTRAINT "service_functions" UNIQUE ("service_id" ASC, "name" ASC)
);

-- ----------------------------
-- Table structure for "main"."soap_function_args"
-- ----------------------------
DROP TABLE "main"."soap_function_args";
CREATE TABLE "soap_function_args" (
"id"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"function_id"  INTEGER NOT NULL,
"name"  TEXT NOT NULL,
"args"  TEXT NOT NULL,
"return"  TEXT,
CONSTRAINT "fkey0" FOREIGN KEY ("function_id") REFERENCES "soap_function" ("id") ON DELETE RESTRICT ON UPDATE RESTRICT
);

-- ----------------------------
-- Table structure for "main"."soap_service"
-- ----------------------------
DROP TABLE "main"."soap_service";
CREATE TABLE "soap_service" (
"id"  INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
"name"  TEXT NOT NULL,
"url"  TEXT NOT NULL,
"login"  TEXT NOT NULL,
"password"  TEXT NOT NULL
);

-- ----------------------------
-- Table structure for "main"."soap_test"
-- ----------------------------
DROP TABLE "main"."soap_test";
CREATE TABLE "soap_test" (
"id"  INTEGER NOT NULL,
"service_id"  INTEGER NOT NULL,
"date_create"  INTEGER NOT NULL,
"date_start"  INTEGER,
"date_end"  INTEGER,
"status"  INTEGER NOT NULL DEFAULT 1,
PRIMARY KEY ("id" ASC),
FOREIGN KEY ("service_id") REFERENCES "soap_service" ("id") ON DELETE RESTRICT ON UPDATE RESTRICT
);

-- ----------------------------
-- Table structure for "main"."soap_test_result"
-- ----------------------------
DROP TABLE "main"."soap_test_result";
CREATE TABLE "soap_test_result" (
"id"  INTEGER NOT NULL,
"function_args_id"  INTEGER NOT NULL,
"test_id"  INTEGER NOT NULL,
"date"  INTEGER NOT NULL,
"result"  INTEGER NOT NULL,
PRIMARY KEY ("id" ASC),
CONSTRAINT "fkey0" FOREIGN KEY ("function_args_id") REFERENCES "soap_function_args" ("id") ON DELETE RESTRICT ON UPDATE RESTRICT,
CONSTRAINT "fkey1" FOREIGN KEY ("test_id") REFERENCES "soap_test" ("id") ON DELETE RESTRICT ON UPDATE RESTRICT
);