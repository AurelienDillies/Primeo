import { ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { Classe } from '../../components/classe/classe';
import { Student } from '../../models/student.model';
import { UserService } from '../../services/user-service';

@Component({
  selector: 'app-classes',
  imports: [Classe],
  templateUrl: './classes.html',
  styleUrl: './classes.css',
})
export class Classes implements OnInit {
  student: Student | null = null;
  teacher: any = null;
  classes: any[] = [];
  token: string | null = null;

  constructor(private userService: UserService, private cdr: ChangeDetectorRef) {}

  ngOnInit() {
    this.token = this.userService.getToken();
    console.log(this.token);
    const userId = this.userService.getUserId();
    if (userId) {
      console.log('Fetching user info for ID:', userId);
      this.userService.getUserInfo(userId).subscribe((userInfo) => {
        console.log('User info received:', userInfo);
        if (userInfo.roles.includes('ROLE_STUDENT')) {
          console.log('User is a student');
          this.student = userInfo as Student;
          this.classes = this.student.classes;
          console.log(this.student);
          console.log();
        } else if (userInfo.roles.includes('ROLE_TEACHER')) {
          this.teacher = userInfo;
          this.classes = this.teacher.classes;
          console.log(this.teacher);
        }
        this.cdr.detectChanges();
      });
    }
  }
    

}
