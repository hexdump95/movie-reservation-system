import {Component, ElementRef, HostListener} from '@angular/core';
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
  userPermissions: string[] = [];
  hideMenu: boolean = true;

  constructor(private authService: AuthService, private eRef: ElementRef) {
  }

  ngOnInit() {
    this.hideMenu = this.isDeviceSmallThan768();
    this.authService.isLoggedIn$.subscribe(status => {
      this.isLoggedIn = status;
    });

    this.authService.user$.subscribe(user => {
      this.user = user;
    });

    this.authService.userRoles$.subscribe(userRoles => {
      this.userRoles = userRoles;
    });

    this.authService.userPermissions$.subscribe(userPermissions => {
      this.userPermissions = userPermissions;
    });
  }

  toggleHamburger() {
    this.hideMenu = !this.hideMenu;
  }


  @HostListener('document:click', ['$event'])
  onClickOutside(event: MouseEvent) {
    if (window.innerWidth < 768) {
      const target = event.target as HTMLElement;

      if (!this.eRef.nativeElement.contains(target)) {
        this.hideMenu = true;
      }
    }
  }

  closeMenu() {
    if (window.innerWidth < 768)
      this.hideMenu = true;
  }

  isDeviceSmallThan768() {
    return window.innerWidth < 768;
  }
}
