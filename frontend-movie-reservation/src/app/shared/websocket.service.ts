import { Injectable } from '@angular/core';
import { Centrifuge, Subscription } from 'centrifuge';
import { BehaviorSubject } from 'rxjs';
import {SeatUpdate} from "./seat-update.interface";

@Injectable({
  providedIn: 'root'
})
export class WebSocketService {
  private centrifuge: Centrifuge | null = null;
  private subscription: Subscription | null = null;
  private seatUpdatesSubject = new BehaviorSubject<SeatUpdate | null>(null);
  private websocketUrl = 'ws://localhost:8000';

  seatUpdates$ = this.seatUpdatesSubject.asObservable();

  constructor() {}

  connect(token: string, channel: string): void {
    this.centrifuge = new Centrifuge(`${this.websocketUrl}/connection/websocket`, {
      token: token
    });

    this.centrifuge.on('connecting', (_) => {
    });

    this.centrifuge.on('connected', (_) => {
    });

    this.centrifuge.on('disconnected', (_) => {
    });

    this.subscription = this.centrifuge.newSubscription(channel);

    this.subscription.on('publication', (ctx) => {
      this.seatUpdatesSubject.next(ctx.data);
    });

    this.subscription.on('subscribing', (_) => {
    });

    this.subscription.on('subscribed', (_) => {
    });

    this.subscription.subscribe();
    this.centrifuge.connect();
  }

  disconnect(): void {
    if (this.subscription) {
      this.subscription.unsubscribe();
    }
    if (this.centrifuge) {
      this.centrifuge.disconnect();
    }
  }
}
