import { Component, Input } from '@angular/core';
import { RouterLink } from '@angular/router';
import { AcademicProgressCard } from '../../models/academic.model';

@Component({
  selector: 'app-advance',
  imports: [RouterLink],
  templateUrl: './advance.html',
  styleUrl: './advance.css',
})
export class Advance {
  @Input() progressCard?: AcademicProgressCard;
}
