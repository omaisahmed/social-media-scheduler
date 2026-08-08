import Alpine from 'alpinejs';

window.Alpine = Alpine;

import.meta.glob(['../../Modules/**/resources/js/*.js', '../../Modules/**/resources/js/**/*.js'], { eager: true });

Alpine.start();
