import { startStimulusApp } from '@symfony/stimulus-bundle';
import ModelLoaderController from './controllers/model_loader_controller.js';
import RegistrationFormatterController from './controllers/registration_formatter_controller.js';
import AdressFormatterController from './controllers/address_formatter_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
app.register('model-loader', ModelLoaderController);
app.register('registration-formatter', RegistrationFormatterController);
app.register('address-formatter', AdressFormatterController);