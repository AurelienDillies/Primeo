import { Component } from '@angular/core';
import { User } from '../../components/user/user';

@Component({
  selector: 'app-users',
  imports: [User],
  templateUrl: './users.html',
  styleUrl: './users.css',
})
export class Users {}
