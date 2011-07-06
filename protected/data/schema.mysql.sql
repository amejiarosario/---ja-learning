-- [0] --
DROP TABLE IF EXISTS tbl_chapters;
DROP TABLE IF EXISTS tbl_tutorials;
DROP TABLE IF EXISTS tbl_users;

-- [1] --
CREATE TABLE IF NOT EXISTS tbl_users (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,
    email VARCHAR(128) NOT NULL,
    name VARCHAR(128),
    lastname VARCHAR(128),
    created_at TIMESTAMP
	
) ENGINE=InnoDB;

-- INSERT INTO tbl_users (username, password, email) VALUES ('adrian', 'recrins', 'adriansky@gmail.com');
-- INSERT INTO tbl_users (username, password, email) VALUES ('test1', 'pass1', 'test1@example.com');
-- INSERT INTO tbl_users (username, password, email) VALUES ('test2', 'pass2', 'test2@example.com');

-- --[2]-----

CREATE TABLE IF NOT EXISTS tbl_tutorials (
    id INTEGER NOT NULL AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    link VARCHAR(255) NOT NULL,
	accessed DATE NOT NULL,
	created_at TIMESTAMP NOT NULL,
	PRIMARY KEY (id, user_id),
	FOREIGN KEY (user_id) REFERENCES tbl_users (id)
            ON DELETE CASCADE 
            ON UPDATE CASCADE
) TYPE=InnoDB;


-- --[3]-----

CREATE TABLE IF NOT EXISTS tbl_chapters (
    id INTEGER NOT NULL AUTO_INCREMENT,
    tutorial_id INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    link VARCHAR(255) NOT NULL,
	accessed DATE NOT NULL,
	created_at TIMESTAMP NOT NULL,
	PRIMARY KEY (id, tutorial_id),
	FOREIGN KEY (tutorial_id) REFERENCES tbl_tutorials (id)
            ON DELETE CASCADE 
            ON UPDATE CASCADE
) TYPE=InnoDB;

