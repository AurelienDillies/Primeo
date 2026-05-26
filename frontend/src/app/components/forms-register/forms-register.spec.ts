import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormsRegister } from './forms-register';

describe('FormsRegister', () => {
  let component: FormsRegister;
  let fixture: ComponentFixture<FormsRegister>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormsRegister],
    }).compileComponents();

    fixture = TestBed.createComponent(FormsRegister);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
