import { Injectable } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { RouterStateSnapshot, TitleStrategy } from '@angular/router';

@Injectable()
export class AppTitleStrategy extends TitleStrategy {

  constructor(private title: Title) {
    super();
  }

  override updateTitle(snapshot: RouterStateSnapshot): void {
    const routeTitle = this.buildTitle(snapshot);

    if (routeTitle) {
      this.title.setTitle(`${routeTitle} – Priméo`);
    } else {
      this.title.setTitle('Priméo');
    }
  }
}