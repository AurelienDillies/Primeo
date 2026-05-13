import { Component } from '@angular/core';
import { FormsRegister } from '../../components/forms-register/forms-register';

@Component({
  selector: 'app-register',
  imports: [FormsRegister],
  templateUrl: './register.html',
  styleUrl: './register.css',
})
export class Register {}
