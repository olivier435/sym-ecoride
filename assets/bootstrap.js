import { startStimulusApp } from '@symfony/stimulus-bundle';
import ModelLoaderController from './controllers/model_loader_controller.js';
import RegistrationFormatterController from './controllers/registration_formatter_controller.js';
import AddressFormatterController from './controllers/address_formatter_controller.js';
import SeatsCounterController from './controllers/seats_counter_controller.js';
import PriceController from './controllers/price_controller.js';
import TripDeleteController from './controllers/trip_delete_controller.js';
import TripSearchController from './controllers/trip_search_controller.js';
import TravelPreferenceController from './controllers/travel_preference_controller.js';
import AjaxFormController from './controllers/ajax_form_controller.js';
import ParticipationController from './controllers/participation_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
app.register('model-loader', ModelLoaderController);
app.register('registration-formatter', RegistrationFormatterController);
app.register('address-formatter', AddressFormatterController);
app.register('seats-counter', SeatsCounterController);
app.register('price', PriceController);
app.register('trip-delete', TripDeleteController);
app.register('trip-search', TripSearchController);
app.register('ajax-form', AjaxFormController);
app.register('travel-preference', TravelPreferenceController);
app.register('participation', ParticipationController);