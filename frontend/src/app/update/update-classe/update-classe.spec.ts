import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UpdateClasse } from './update-classe';
import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';

describe('UpdateClasse', () => {
  let component: UpdateClasse;
  let fixture: ComponentFixture<UpdateClasse>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UpdateClasse],
      providers: [provideHttpClient(), provideHttpClientTesting(), provideRouter([])],
    }).compileComponents();

    fixture = TestBed.createComponent(UpdateClasse);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
