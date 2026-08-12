import { bootstrapApplication } from '@angular/platform-browser';
import { appConfig } from './app.config';
import { AppComponent } from './app.component'; // ¡Asegúrate de que apunte a nuestro archivo!

bootstrapApplication(AppComponent, appConfig)
  .catch((err) => console.error(err));
