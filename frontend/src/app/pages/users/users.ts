import { Component } from '@angular/core';
import { User } from '../../components/user/user';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-users',
  imports: [User, RouterLink],
  templateUrl: './users.html',
  styleUrl: './users.css',
})
export class Users {}
