CREATE TABLE "networks" ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "network_name" varchar(255), "pay_type" varchar(255), "created_at" datetime, "updated_at" datetime, "enabled" boolean DEFAULT 1, "supports_threshold" boolean DEFAULT 0, "always_fill" boolean DEFAULT 1);
CREATE TABLE "publishers" ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "publisher_name" varchar(255), "website" varchar(255), "created_at" datetime, "updated_at" datetime);
CREATE TABLE "schema_migrations" ("version" varchar(255) NOT NULL);
CREATE TABLE "tags" ("id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "tag_name" varchar(255), "network_id" integer, "publisher_id" integer, "value" decimal(3,2), "enabled" boolean DEFAULT 1, "always_fill" boolean DEFAULT 1, "sample_rate" integer(3), "tier" integer(3), "frequency_cap" integer(3), "rejection_cap" integer(3), "rejection_time" integer(3), "tag" text);
CREATE UNIQUE INDEX "unique_schema_migrations" ON "schema_migrations" ("version");
INSERT INTO schema_migrations (version) VALUES ('20090904211146');

INSERT INTO schema_migrations (version) VALUES ('20090904211256');

INSERT INTO schema_migrations (version) VALUES ('20090904215518');