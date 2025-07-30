import {Component} from '@angular/core';
import {MatButton} from "@angular/material/button";
import {RouterLink} from "@angular/router";
import {TheaterService} from "../../core/theater.service";
import {TheaterResponse} from "../../core/theater-response";
import {MatDialog} from "@angular/material/dialog";
import {ConfirmDialogComponent} from "../../shared/confirm-dialog/confirm-dialog.component";
import {TheaterDetailDialogComponent} from "./theater-detail-dialog/theater-detail-dialog.component";

@Component({
  selector: 'app-theater',
  standalone: true,
  imports: [
    MatButton,
    RouterLink
  ],
  templateUrl: './theater.component.html',
  styleUrl: './theater.component.css'
})
export class TheaterComponent {
  theaters!: TheaterResponse[];

  constructor(
    private theaterService: TheaterService,
    private dialog: MatDialog
  ) {
  }

  ngOnInit() {
    this.theaterService.getTheaters().subscribe(res => {
        this.theaters = res;
      }
    );
  }

  openDeleteDialog(theater: TheaterResponse) {
    const dialogRef = this.dialog.open(ConfirmDialogComponent, {
      data: {
        title: `Delete Theater #'${theater.number}'`,
        message: 'Are you sure you want to delete this theater?'
      },
      disableClose: true
    });
    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.theaterService.deleteTheater(theater.id)
          .subscribe(res => {
            if (res.success) {
              this.theaters = this.theaters.filter(x => x.id !== theater.id);
            }
          });
      }
    });
  }

  openDetailDialog(theaterId: number) {
    const dialogRef = this.dialog.open(TheaterDetailDialogComponent, {
      data: theaterId,
      disableClose: true
    });
  }

}
