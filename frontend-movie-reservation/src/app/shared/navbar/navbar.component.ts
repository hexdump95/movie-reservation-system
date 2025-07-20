import {Component} from '@angular/core';
import {RouterLink} from "@angular/router";
import {MatToolbar} from "@angular/material/toolbar";
import {MatButton} from "@angular/material/button";
import {AuthService} from "../../auth/auth.service";

@Component({
  selector: 'app-navbar',
  standalone: true,
  imports: [
    RouterLink,
    MatButton,
    MatToolbar
  ],
  templateUrl: './navbar.component.html',
  styleUrl: './navbar.component.css'
})
export class NavbarComponent {
  isLoggedIn = false;
  user: any = null;
  userRoles: string[] = [];

  constructor(
    private authService: AuthService,
  ) {}

  ngOnInit() {
    this.authService.checkAuthStatus();

    this.authService.isLoggedIn$.subscribe(status => {
      this.isLoggedIn = status;
    });

    this.authService.user$.subscribe(user => {
      this.user = user;
    });

    this.authService.userRoles$.subscribe(userRoles => {
      this.userRoles = userRoles;
    });
  }
}
