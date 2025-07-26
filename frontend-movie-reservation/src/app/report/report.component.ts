import {Component} from '@angular/core';
import {ReportService} from "./report.service";
import {CurrencyPipe, DatePipe} from "@angular/common";

@Component({
  selector: 'app-report',
  standalone: true,
  imports: [
    DatePipe,
    CurrencyPipe
  ],
  templateUrl: './report.component.html',
  styleUrl: './report.component.css'
})
export class ReportComponent {
  report: any;

  constructor(private reportService: ReportService,) {
  }

  ngOnInit() {
    this.reportService.getRevenue().subscribe(report => {
      this.report = report;
    });
  }

}
