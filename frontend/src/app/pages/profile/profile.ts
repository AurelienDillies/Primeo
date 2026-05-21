import { Component } from '@angular/core';
import { FormsProfile } from '../../components/forms-profile/forms-profile';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-profile',
  imports: [FormsProfile, RouterLink],
  templateUrl: './profile.html',
  styleUrl: './profile.css',
})
export class Profile {}
