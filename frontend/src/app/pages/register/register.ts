import { Component } from '@angular/core';
import { FormsRegister } from '../../components/forms-register/forms-register';
import { RouterLink } from '@angular/router';


@Component({
  selector: 'app-register',
  imports: [FormsRegister, RouterLink],
  templateUrl: './register.html',
  styleUrl: './register.css',
})
export class Register {}
