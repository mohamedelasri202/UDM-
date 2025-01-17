CREATE DATABASE udm ;
USE DATABASE udm ;
create table  role(
    id int primary key AUTO_INCREMENT,
    name varchar (50)
);
CREATE TABLE user (
    ID INT PRIMARY KEY AUTO_INCREMENT ,
    name varchar (50),
    last_name varchar (50),
    email varchar (50),
    password varchar (255) ,
    status enum ("actevated","waiting","suspended"),
    role_id int ,
  FOREIGN KEY (role_id) REFERENCES role(id) 

);
 CREATE TABLE categories (
    id int primary key AUTO_INCREMENT,
    title varchar (50),
    description  VARCHAR (255)
 );
 create table courses (
    id int  primary key AUTO_INCREMENT,
    title varchar  (50),
    description varchar (100),
    contonu varchar (255),
    price decimal (5,2),
    id_user int ,
    id_categorie int ,
    FOREIGN key (id_user) REFERENCES user(id),
    FOREIGN key (id_categorie) REFERENCES categories(id) 
 );

 create table tags (
     id INT  primary key AUTO_INCREMENT ,
     title varchar (50)
 );
create table inscription (
    id_user int ,
    id_course int ,
    FOREIGN key (id_user)REFERENCES user (id),
    FOREIGN key (id_course) REFERENCES courses (id)
);
create table tagscours (
     id_tag int ,
     id_cours int ,
     FOREIGN key (id_tag)REFERENCES tags(id),
     FOREIGN key (id_cours) REFERENCES courses (id)
);
