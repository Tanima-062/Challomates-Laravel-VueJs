
import { usePage } from '@inertiajs/inertia-vue3'
import dayjs from "dayjs";
import countries from 'i18n-iso-countries'
// Support french & english languages.
countries.registerLocale(require("i18n-iso-countries/langs/en.json"));
// countries.registerLocale(require("i18n-iso-countries/langs/fr.json"));
countries.registerLocale(require("i18n-iso-countries/langs/de.json"));

export default {
    computed: {
        permissions() {
            return usePage().props.value.permissions;
        },
        auth_user() {
            return usePage().props.value.auth_user;
        }
    },
    methods: {
        hasPermission(permision) {
            return this.permissions.includes(permision)
        },
        hasAnyPermissions(permisions) {
            let find = false;
            for (let i = 0; i < permisions.length; i++) {
                if (this.permissions.includes(permisions[i])) {
                    find = true;
                    break;
                }
            }

            return find;
        },
        formateDate(date, formate = 'DD.MM.YYYY') {
            return dayjs(date).format(formate).toString()
        },

        timeDifference(start_time, end_time, unit) {
            const date1 = start_time;
            const date2 = dayjs(end_time);
            let diff = date2.diff(date1, 'day', true);



            if (unit == 'day') {
                diff = Math.floor(diff);
            } else if (unit == 'hour') {
                const days = Math.floor(diff);
                const hours = Math.floor((diff - days) * 24);
                diff = hours;
            }
            else if (unit == 'minute') {
                const minuteDiff = date2.diff(date1, 'minutes');
                const days = Math.floor(diff);
                const hours = Math.floor((diff - days) * 24);
                const minute = ((days * 24) + hours) * 60

                diff = Number(minuteDiff) - Number(minute)
            }
            else if (unit == 'second') {
                const minuteDiff = date2.diff(date1, 'minutes');
                const days = Math.floor(diff);
                const hours = Math.floor((diff - days) * 24);


                const minutes = Number(minuteDiff) - Number(((days * 24) + hours) * 60)

                const secondsDiff = date2.diff(date1, 'seconds');

                const seconds = (minutes * 60) + (hours * 60 * 60) + (days * 24 * 60 * 60)

                diff = secondsDiff - seconds;
            }

            if (diff < 10) {
                diff = `0${diff}`
            }

            return diff;
        },
        ensureNumberIsFormated(e, float = false) {
            if(e.target.value == '') {
                return
            }
            if (float === false) {
                e.target.value = e.target.value.replace(/\D/g, '')
            }
            if (typeof float == 'number' && e.target.value.split('.')[1]?.length > float) {
                e.target.value = Number(e.target.value).toFixed(float)
            }
        },
        getCountryName(countryCode, locale = 'de'){
            let localeCode = locale ? locale : usePage().props.value.auth_user.language.code;

            return countries.getName(countryCode.toString().toUpperCase(), localeCode.toString().toLowerCase())
        },
        getCountryCode(countryName, locale = 'de'){
            let localeCode = locale ? locale : usePage().props.value.auth_user.language.code;

            return countries.getAlpha2Code(countryName.toString(), localeCode.toString().toLowerCase())
        },
        getWeekNameTranslation(week) {
            const weeks = {'monday': 'Montag', 'tuesday': 'Dienstag', 'wednesday': 'Mittwoch', 'thursday': 'Donnerstag', 'friday': 'Freitag', 'saturday': 'Samstag', 'sunday': 'Sonntag'};
            return weeks[week];
        },

        getStatusText(status){
            switch (status) {
                case 'active':
                    return 'Aktiv'

                case 'inactive':
                    return 'Inaktiv'

                case 'pending':
                    return 'Anstehend'

                case 'registration pending':
                    return 'Registrierung pendent'

                case 'canceled':
                    return 'Abgebrochen'

                case 'expired':
                    return 'Abgelaufen'

                case 'new':
                    return "Neu"
                default:
                    return "Status"
            }
        }


    },

}

