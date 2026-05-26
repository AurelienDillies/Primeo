import { Component } from '@angular/core';
import { Activitie } from '../../components/activitie/activitie';

@Component({
  selector: 'app-activities',
  imports: [Activitie],
  templateUrl: './activities.html',
  styleUrl: './activities.css',
})
export class Activities {}
