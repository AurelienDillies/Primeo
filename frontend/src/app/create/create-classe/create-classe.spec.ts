import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CreateClasse } from './create-classe';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';

describe('CreateClasse', () => {
  let component: CreateClasse;
  let fixture: ComponentFixture<CreateClasse>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CreateClasse],
      providers: [provideHttpClient(), provideHttpClientTesting(), provideRouter([])],
    }).compileComponents();

    fixture = TestBed.createComponent(CreateClasse);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
