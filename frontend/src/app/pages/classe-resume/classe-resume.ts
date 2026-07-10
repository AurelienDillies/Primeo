import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Classe } from '../../components/classe/classe';
import { UserService } from '../../services/user-service';
import { AcademicClass } from '../../models/academic.model';
import { AcademicStructureService } from '../../services/academic-structure.service';

@Component({
  selector: 'app-classe-resume',
  imports: [CommonModule, Classe],
  templateUrl: './classe-resume.html',
  styleUrl: './classe-resume.css',
})
export class ClasseResume {
  private readonly userService = inject(UserService);
  private readonly academicStructureService = inject(AcademicStructureService);
  classes: AcademicClass[] = [];

  constructor() {
    const userId = this.userService.getUserId();

    if (!userId) {
      return;
    }

    this.userService.getUserInfo(userId).subscribe({
      next: (userInfo: any) => {
        this.classes = this.academicStructureService.getClasses(userInfo);
      },
      error: () => {
        this.classes = [];
      }
    });
  }
}