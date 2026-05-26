import { Component } from '@angular/core';
import { Course } from '../../components/course/course';

@Component({
  selector: 'app-courses',
  imports: [Course],
  templateUrl: './courses.html',
  styleUrl: './courses.css',
})
export class Courses {}
