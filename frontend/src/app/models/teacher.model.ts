import { User } from "./user.model";

export interface Teacher extends User {
    subject: string;
    teachingClasses: {
        id: number;
        className: string;
        classDescription: string;
        courses: {
            id: number;
            courseTitle: string;
            courseDescription: string;
            courseResourcesfile: string;
            courseVideoUrl: string;
            activities: {
                id: number;
                activityType: string;
                activityTitle: string;
                activityDescription: string;
                activityDate: Date;
                progress: {
                    id: number;
                    progressPercent: number;
                    progressGrade: string;
                    student: {
                        id: number;
                        last_name: string;
                        first_name: string;
                        email: string;
                    };
                }[];
            }[];
            progresses: {
                id: number;
                progressPercent: number;
                progressGrade: string;
                student: {
                    id: number;
                    last_name: string;
                    first_name: string;
                    email: string;
                };
            }[];
            reports: {
                id: number;
                reportTitle: string;
                reportDescription: string;
                reportResourcesfile: string;
                student: {
                    id: number;
                    last_name: string;
                    first_name: string;
                    email: string;
                };
            }[];
        }[];
    }[];
}