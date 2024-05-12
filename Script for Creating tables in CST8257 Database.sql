create table Student
(
    StudentId varchar(16)  NOT NULL PRIMARY KEY,
    Name      varchar(256) NOT NULL,
    Phone     varchar(16),
    Password  varchar(256)
);

create table Course
(
    CourseCode  varchar(10)  NOT NULL PRIMARY KEY,
    Title       varchar(256) NOT NULL,
    WeeklyHours int          NOT NULL
);

create table Semester
(
    SemesterCode varchar(10) NOT NULL PRIMARY KEY,
    Term         varchar(10) NOT NULL,
    Year         int         NOT NULL
);
create table CourseOffer
(
    CourseCode   varchar(10) NOT NULL,
    SemesterCode varchar(10) NOT NULL,
    PRIMARY KEY (CourseCode, SemesterCode),
    FOREIGN KEY (SemesterCode) REFERENCES Semester (SemesterCode),
    FOREIGN KEY (CourseCode) REFERENCES Course (CourseCode)
);
create table Registration
(
    StudentId    varchar(16) NOT NULL,
    CourseCode   varchar(10) NOT NULL,
    SemesterCode varchar(10) NOT NULL,
    PRIMARY KEY (StudentId, CourseCode),
    FOREIGN KEY (StudentId) REFERENCES Student (StudentId),
    FOREIGN KEY (CourseCode) REFERENCES CourseOffer (CourseCode),
    FOREIGN KEY (SemesterCode) REFERENCES CourseOffer (SemesterCode)
);



CREATE TRIGGER update_student_last_updated
BEFORE INSERT ON Student
FOR EACH ROW
BEGIN
    SET NEW.last_updated = NOW();
END;