import { User } from "./user.model";

export interface Student extends User {
    enrollmentDate: string;
    classes:{
        id: number;
        classname: string;
        classdescription: string;
    }[];
    teacher: {
        id: number;
        last_name: string;
        first_name: string;
        email: string;
        subject: string;
    };
    progresses: {
        id: number;
        progressPercent: number;
        progressGrade: string;
        classes: {
            id: number;
            classname: string;
            classdescription: string;
        };
        course: {
            id: number;
            coursename: string;
            coursedescription: string;
        };
    }[];
    parents: {
        id: number;
        last_name: string;
        first_name: string;
        email: string;
    }[];


}