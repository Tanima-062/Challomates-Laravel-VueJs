<template>
    <div class="challo__card challo__card-p6 rounded-b-3xl">
        <Back :target="route('boosters.index')" :backPrevUrl="true" :show-modal="form.isDirty"/>
        <h5 class="page-title">{{ $t("Booster bearbeiten") }}</h5>
        <div class="form-wrapper">
            <form class="classic-form" @submit.prevent="update">
                <div class="form-field">
                    <div class="input-wrapper w-1/3 ">
                        <h5 class="detail__label mb-2">{{$t('Erstellungsdatum')}}</h5>
                        <span class="detail__value">{{formateDate(booster.created_at)}}</span>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <h5 class="detail__label mb-2">{{ $t("Booster-ID") }}*</h5>
                        <span class="detail__value">{{ booster.prefix_id }}</span>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <label>Vertriebspartner*</label>
                        <div class="select-wrapper">
                            <select class='challo__input' name="sales_partner" v-model="form.sales_partner" :style="form_error.sales_partner.error==true ? 'border-color:red' : ''" @change="getContract($event);form_error.sales_partner.error==false">
                              <option value='' hidden>Vertriebspartner auswählen</option>
                              <OptionComponent v-for="sales_partner in sales_partners" :key="sales_partner.id" name="company_name" :option="sales_partner"/>
                            </select>
                        </div>
                        <div class="form-error" v-if="v$.sales_partner.$invalid">{{ v$.sales_partner.$message }}</div>
                    </div>

                    <div class="input-wrapper w-1/3">
                        <label for="contract_id">Vertrag*</label>
                        <SearchSelect v-model="form.contract" :options="contracts" valueKey="id"
                            :placeholder="contracts_placeholder" labelKey="name"
                            :searchable="['prefix_id', 'name']"
                            :error="form.errors.contract"
                            @change="form_error.contract.error=false"
                            @showOptions="(value) => (contracts_placeholder = value ? 'Nach Vertrag oder -ID suchen' : 'Vertrag auswählen')">
                            <template #label="{ label }">
                                <div class="flex justify-between">
                                    <span>{{ label }}</span>
                                    <a v-if="form.contract"
                                        :href="route('contract.show', { contract: form.contract })"
                                        class="text-gray-3 text-16 underline underline-offset-2 font-ropa"
                                        target="_blank">
                                        Details ansehen
                                    </a>
                                </div>
                            </template>
                            <template #option="{ option, isSelected }">
                                <div class="item flex justify-between pl-5 pr-6 pt-[11px] pb-3"
                                    :class="{ 'bg-gray-corner': isSelected }">
                                    <p class="label text-tints-5 text-16 font-ropa">
                                        {{ option.name }}
                                    </p>
                                    <a :href="route('contract.show', { contract: option.id })"
                                        class="text-gray-3 text-16 underline underline-offset-2 font-ropa"
                                        target="_blank">
                                        Details ansehen
                                    </a>
                                </div>
                            </template>
                        </SearchSelect>
                        <div class="form-error" v-if="v$.contract.$invalid">{{ v$.contract.$message }}</div>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper">
                        <div class="challo__textarea">
                            <label>Titel*</label>
                            <span class="form-error float-right" v-if="v$.title.maxLength.$invalid">{{ v$.title.maxLength.$message }}</span>
                            <input type="text" name="title" style="border:none; margin-left:10px; width:80%; outline:none;" v-model="form.title" maxlength="41" @keyup="form_error.title.error=false">
                            <hr class="mt-4" :style="form_error.title.error==true || v$.title.maxLength.$invalid == true ? 'border-color:red' : ''">
                            <textarea class="mt-2 h-36" name="body" v-model="form.body" maxlength="251" placeholder="Maximal 250 Zeichen möglich"></textarea>
                            <div class="form-error" v-if="v$.body.maxLength.$invalid">{{ v$.body.maxLength.$message }}</div>
                            <hr class="mb-4" :style="form_error.body.error==true || v$.body.maxLength.$invalid==true ? 'border-color:red' : ''">
                            <input type="file" name="file" ref="file" id="fileInput" style="display:none" @change="uploadFile($event)" accept="image/*">
                            <div class="flex">
                                <div class="flex w-2/3">
                                    <img :src="form.file == '' ? '/images/image.png' : form.file" width=20 height=20 style="float:left; margin-right: 10px; margin-bottom: 3px;" @click="$refs.file.click()" id="img"/><span style="float:left" id="filename-show">{{(form.file == '') ? $t('Bild hochladen') : form.file_name}}</span>
                                </div>
                                <span @click="removeFile()" :style="form.file == '' ? 'display:none' : 'display:block'" id="remove" class="flex w-1/3">
                                    <span style="float:left; margin-left: 170px; margin-right: 10px; margin-top: 3px;"><svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.66876 7.66051C2.48126 7.29116 2.02985 7.14374 1.66051 7.33124C1.29116 7.51874 1.14374 7.97015 1.33124 8.33949L2.66876 7.66051ZM4.46665 10.3667L4.13792 11.0408V11.0408L4.46665 10.3667ZM8.05061 10.9505L7.95823 10.2062L8.05061 10.9505ZM13.4655 8.76943C13.7903 8.51233 13.8451 8.04064 13.588 7.71587C13.3309 7.3911 12.8592 7.33625 12.5345 7.59335L13.4655 8.76943ZM12.25 11C12.25 11.4142 12.5858 11.75 13 11.75C13.4142 11.75 13.75 11.4142 13.75 11H12.25ZM13 8H13.75C13.75 7.58579 13.4142 7.25 13 7.25V8ZM10 7.25C9.58579 7.25 9.25 7.58579 9.25 8C9.25 8.41421 9.58579 8.75 10 8.75V7.25ZM11.3312 4.33949C11.5187 4.70884 11.9701 4.85626 12.3395 4.66876C12.7088 4.48126 12.8563 4.02985 12.6688 3.66051L11.3312 4.33949ZM9.53335 1.63331L9.86208 0.959185V0.959185L9.53335 1.63331ZM5.94939 1.04949L6.04176 1.79378L5.94939 1.04949ZM0.534482 3.23057C0.209716 3.48767 0.15486 3.95936 0.411958 4.28413C0.669057 4.6089 1.14075 4.66375 1.46552 4.40666L0.534482 3.23057ZM1.75 1C1.75 0.585786 1.41421 0.25 1 0.25C0.585786 0.25 0.25 0.585786 0.25 1H1.75ZM1 4H0.25C0.25 4.41421 0.585786 4.75 1 4.75L1 4ZM4 4.75C4.41421 4.75 4.75 4.41421 4.75 4C4.75 3.58579 4.41421 3.25 4 3.25V4.75ZM1.33124 8.33949C1.9207 9.50066 2.91208 10.443 4.13792 11.0408L4.79537 9.69257C3.83857 9.226 3.09856 8.50715 2.66876 7.66051L1.33124 8.33949ZM4.13792 11.0408C5.36323 11.6383 6.76775 11.8655 8.14298 11.6948L7.95823 10.2062C6.86383 10.342 5.7527 10.1594 4.79537 9.69257L4.13792 11.0408ZM8.14298 11.6948C10.4204 11.4122 12.0183 9.91514 13.4655 8.76943L12.5345 7.59335C10.9324 8.86162 9.71708 9.98794 7.95823 10.2062L8.14298 11.6948ZM13.75 11V8H12.25V11H13.75ZM13 7.25H10V8.75H13V7.25ZM12.6688 3.66051C12.0793 2.49934 11.0879 1.55695 9.86208 0.959185L9.20463 2.30743C10.1614 2.774 10.9014 3.49285 11.3312 4.33949L12.6688 3.66051ZM9.86208 0.959185C8.63677 0.361673 7.23224 0.134522 5.85702 0.305198L6.04176 1.79378C7.13617 1.65795 8.2473 1.8406 9.20463 2.30743L9.86208 0.959185ZM5.85702 0.305198C3.57962 0.587841 1.98175 2.08486 0.534482 3.23057L1.46552 4.40666C3.0676 3.13838 4.28292 2.01206 6.04176 1.79378L5.85702 0.305198ZM0.25 1V4H1.75V1H0.25ZM1 4.75H4V3.25H1V4.75Z" fill="#787878"/></svg></span>
                                    <span id="remove-msg-show">Bild entfernen</span>
                                </span>
                            </div>
                            <p class="form-error" id="img-error"></p>
                        </div>
                    </div>
                </div>

                <div class="form-field">
                    <div class="input-wrapper w-1/3">
                        <TextInputWithPrefixAndSuffixText
                            placeholder="00.00"
                            :buttonSuffixText="`Km`"
                            :label="`${$t('Reichweite')}*`"
                            v-model="form.range"
                            :error="form.errors.range"
                            @clearError="form.errors.range = null"
                            :show_error="form_error.range.error"
                            @keypress="isNumber($event);"
                            @keyup="form_error.range.error=false"
                        />
                    </div>

                    <div class="input-wrapper w-1/3">
                        <label>Boosterart*</label>
                            <select class='challo__input mt-1' name="booster_type" v-model="form.booster_type" :style="form_error.booster_type.error==true ? 'border-color:red' : ''">
                              <option value='' hidden>Boosterart auswählen</option>
                              <option value='One Time'>Einmalig</option>
                              <option value='Recurring'>Wiederkehrend</option>
                            </select>
                        <div class="form-error" v-if="v$.booster_type.$invalid">{{ v$.booster_type.$message }}</div>
                    </div>
                    <div v-if="form.booster_type=='One Time'" class="input-wrapper w-1/3">
                        <label>Postingzeitpunkt*</label>
                        <DateTimePicker
                            v-model="form.posting_time"
                            @clearError="form.errors.posting_time = null"
                            :error="form.errors.posting_time"
                            :show_error="form_error.posting_time.error"
                        />
                    </div>
                    <div v-if="form.booster_type=='Recurring'" class="input-wrapper w-1/3">
                        <TextInput
                            id="number-boosters"
                            :placeholder="$t('Zahl eingeben')"
                            :label="`${$t('Anzahl Booster / Monat')}*`"
                            v-model="form.number_of_boosters_month"
                            :error="form.errors.number_of_boosters_month"
                            @clearError="form.errors.number_of_boosters_month = null"
                            :show_error="form_error.booster_type.error"
                            @keyup="formGenerate($event);form_error.booster_type.error=false"
                            @keypress="isDigit($event);"
                        />
                    </div>
                </div>
                <div v-if="form.booster_type=='Recurring' && form.number_of_boosters_month==1">
                    <div class="form-field">
                        <div class="input-wrapper w-full">
                            <label>{{$t("Jeweiliger Postingzeitpunkt")}}*</label>
                        </div>
                    </div>
                    <div class="form-field">
                        <div class="input-wrapper w-1/4 flex items-center">
                            <span class="mr-1 text-sm" style="color: #1AA1E4;">Am</span>
                            <select class='challo__input week' v-model="form.weeks[0]" @change="$event.target.style.borderColor=''">
                                <option value='' hidden>Auswählen</option>
                                <option value='1st'>Ersten</option>
                                <option value='2nd'>Zweiten</option>
                                <option value='3rd'>Dritten</option>
                                <option value='4th'>Vierten</option>
                                <option value='last'>Letzten</option>
                            </select>
                        </div>
                        <div class="input-wrapper w-1/4">
                            <select class='challo__input weekday' v-model="form.weekdays[0]" @change="$event.target.style.borderColor=''">
                                <option value='' hidden>Wochentag auswählen</option>
                                <option value='Monday'>Montag</option>
                                <option value='Tuesday'>Dienstag</option>
                                <option value='Wednesday'>Mittwoch</option>
                                <option value='Thursday'>Donnerstag</option>
                                <option value='Friday'>Freitag</option>
                                <option value='Saturday'>Samstag</option>
                                <option value='Sunday'>Sonntag</option>
                            </select>
                        </div>
                        <div class="input-wrapper w-1/4 flex items-center">
                            <span class="mr-1 text-sm" style="color: #1AA1E4;">um</span>
                            <input type="text" class="challo__input pl-4 pr-2 time w-1/2 bg-[url('/images/clock.png')] bg-no-repeat bg-right bg-origin-content p-1" v-model="form.times[0]" id='time-0' @keyup="validateHhMm($event, 'time-0');" placeholder="00:00">
                            <span class="ml-1 text-sm" style="color: #1AA1E4;">Uhr jeden Monat</span>
                        </div>
                    </div>
                </div>
                <div v-if="form.booster_type=='Recurring' && form.number_of_boosters_month > 1">
                    <div class="form-field">
                        <div class="input-wrapper w-full">
                            <label>{{$t("Postingzeitpunkte")}}*</label>
                        </div>
                    </div>
                    <div v-for="(item, loopIndex) in parseInt(form.number_of_boosters_month)" :key="loopIndex">
                        <div class="input-wrapper"><label>{{loopIndex+1}}. Booster</label></div>
                        <div class="form-field">
                            <div class="input-wrapper w-1/4 flex items-center">
                                <span class="mr-1 text-sm" style="color: #1AA1E4;">Am</span>
                                <select class='challo__input week' v-model="form.weeks[loopIndex]" @change="$event.target.style.borderColor=''">
                                    <option value='' hidden>Auswählen</option>
                                    <option value='1st'>Ersten</option>
                                    <option value='2nd'>Zweiten</option>
                                    <option value='3rd'>Dritten</option>
                                    <option value='4th'>Vierten</option>
                                    <option value='last'>Letzten</option>
                                </select>
                            </div>
                            <div class="input-wrapper w-1/4 mt-0.5">
                                <select class='challo__input weekday' v-model="form.weekdays[loopIndex]" @change="$event.target.style.borderColor=''">
                                    <option value='' hidden>Wochentag auswählen</option>
                                    <option value='Monday'>Montag</option>
                                    <option value='Tuesday'>Dienstag</option>
                                    <option value='Wednesday'>Mittwoch</option>
                                    <option value='Thursday'>Donnerstag</option>
                                    <option value='Friday'>Freitag</option>
                                    <option value='Saturday'>Samstag</option>
                                    <option value='Sunday'>Sonntag</option>
                                </select>
                            </div>
                            <div class="input-wrapper w-1/4 mt-0.5 flex items-center">
                                <span class="mr-1 text-sm" style="color: #1AA1E4;">um</span>
                                <input type="text" class="challo__input pl-4 pr-2 time w-1/2 bg-[url('/images/clock.png')] bg-no-repeat bg-right bg-origin-content p-1" v-model="form.times[loopIndex]" :id="'time-'+loopIndex" @keyup="validateHhMm($event, 'time-'+loopIndex);" placeholder="00:00">
                                <span class="ml-1 text-sm" style="color: #1AA1E4;">Uhr jeden Monat</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="form.booster_type=='Recurring'">
                    <div class="form-field">
                        <div class="input-wrapper-new">
                            <label for="">{{ $t("Laufzeit") }}</label>
                        </div>
                    </div>
                    <div class="form-field flex w-full">
                        <div class="input-wrapper flex">
                            <span class="mr-2 mt-2.5 text-sm" style="color: #1AA1E4;">Beginn</span>
                            <span class="w-96">
                                <DateTimePicker
                                    placeholder='TT.MM.JJJJ'
                                    label="Beginn*"
                                    mode="date"
                                    v-model="form.start"
                                    @clearError="form.errors.start = null"
                                    :error="form.errors.start"
                                    :show_error="form_error.start.error"
                                    :icon="true"
                                />
                            </span>
                        </div>
                        <div class="input-wrapper flex">
                            <span class="mr-2 mt-2.5 text-sm" style="color: #1AA1E4;">Endet am</span>
                            <span class="w-96">
                                <DateTimePicker
                                    placeholder='TT.MM.JJJJ'
                                    label="Endet am*"
                                    mode="date"
                                    v-model="form.end"
                                    @clearError="form.errors.end = null"
                                    :error="form.errors.end"
                                    :show_error="form_error.end.error"
                                    :icon="true"
                                />
                            </span>
                        </div>
                    </div>
                </div>
                <div class="form-field row">
                    <div class="input-wrapper w-1/3">
                        <button class="btn-block challo__btn btn-primary" type="submit" :disabled="form.processing">
                            {{ $t("Speichern") }}
                        </button>
                    </div>
                    <div class="input-wrapper w-1/3">
                        <Cancel
                            :target="route('boosters.index')"
                            message="Änderungen verwerfen?"
                            text="Wenn Sie zurückgehen oder abbrechen, ohne zu speichern, werden alle Änderungen verworfen. Sind Sie sicher, dass Sie die Änderungen wirklich verwerfen wollen?"
                            :show-modal="form.isDirty"
                            :backPrevUrl="true"
                            :staticBackdrop="true"
                        />
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
    import { reactive, ref } from "@vue/reactivity";
    import SwitchInput from "../../Components/Form/Switch.vue";
    import TextInput from "../../Components/Form/TextInput.vue";
    import { useForm } from "@inertiajs/inertia-vue3";
    import Cancel from "../../Components/Form/Cancel.vue";
    import { computed, inject } from "@vue/runtime-core";
    import FieldMissing from "../../Components/Modal/Content/FieldMissing.vue";
    import BoosterList from "../../Components/Modal/Content/BoosterList.vue";
    import PhoneInputWithCounryCode from "../../Components/PhoneInputWithCounryCode.vue";
    import useVuelidate from "@vuelidate/core";
    import { maxLength, required, helpers } from "@vuelidate/validators";
    import TextInputWithPrefixAndSuffixText from "../../Components/Form/TextInputWithPrefixAndSuffixText.vue";
    import OptionComponent from "./Components/OptionComponent.vue";
    import DateTimePicker from "../../Components/Form/DatePickers/DateTimePicker.vue";
    import TimePicker from "../../Components/Form/TimePicker.vue";
    import SearchSelect from "../../Components/Form/SearchSelect.vue";
    import { onMounted, watch } from "@vue/runtime-core";
    import Back from "../../Components/Form/Back.vue";
    import axios from "axios";
    import dayjs from "dayjs";

    const props = defineProps(["sales_partners", "contracts", "booster"]);
    const modal = inject("modal");
    const weeks = [];
    const weekdays = [];
    const times = [];
    const form = useForm({
        sales_partner: props.booster.sales_partner_id,
        contract: props.booster.contract_id,
        title: props.booster.title,
        body: props.booster.body,
        range: props.booster.range,
        booster_type: props.booster?.type,
        number_of_boosters_month: props?.booster.booster_booster_types[0]?.number_of_boosters_month,
        file: props.booster.file_url,
        file_name: props.booster.file_name,
        posting_time: props.booster?.posting_time,
        start: dayjs(props.booster?.start).format('YYYY-MM-DD'),
        end: dayjs(props.booster?.end).format('YYYY-MM-DD'),
        weeks: weeks,
        weekdays: weekdays,
        times: times,
        _method: "PUT",
    })

    const contracts = ref(props.contracts);
    const date = ref(new Date().toJSON().slice(0,10).replace(/-/g,'-'));
    const contracts_placeholder = ref("Vertrag auswählen");

    const inviteable = computed(() => {
        if(form.file == ""){
            document.getElementById("img-error").innerHTML="Bild muss ausgefüllt werden."
            return false
        }
        if(form.sales_partner == "" || form.contract == "" || form.title == "" || form.body == "" || form.body == null || form.range == "" || form.booster_type == "") {
            return false
        } else {
            if(form.booster_type == 'Recurring'){
                if(form.number_of_boosters_month == "" || form.start=="" || form.end==""){
                    if(form.number_of_boosters_month == ""){
                        form_error['number_of_boosters_month']['error'] = true
                        document.getElementById("number-boosters").style.borderColor="red"
                    }
                    if(form.start==""){
                        form_error['start']['error'] = true
                    }
                    if(form.end==""){
                        form_error['end']['error'] = true
                    }
                    return false
                }
            }
            else if(form.booster_type == 'One Time'){
                if(form.posting_time==""){
                    form_error['posting_time']['error'] = true
                    return false
                }
            }
            return true
        }
    })

    const form_error = reactive({
        sales_partner: {error:false},
        contract: {error:false},
        title: {error:false},
        body:{error:false},
        booster_type: {error:false},
        range: {error:false},
        posting_time: {error:false},
        start: {error:false},
        end: {error:false},
        number_of_boosters_month: {error:false},
    });
    const rules = {
        sales_partner: {required},
        contract: {required},
        title: {
            required,
            maxLength: helpers.withMessage( 'Maximal 40 Zeichen möglich', maxLength(40) )
        },
        body:{
            required,
            maxLength: helpers.withMessage( 'Maximal 250 Zeichen möglich', maxLength(250) )
        },
        booster_type: {required},
        range: {required},
        posting_time: {required},
        start: {required},
        end: {required}
    };

    const v$ = useVuelidate(rules, form);

    const isNumber = (evt) =>{
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if ((charCode < 48 || charCode > 57 || form.range.split('.')[1]?.length > 1) && charCode !== 46) {
            evt.preventDefault();;
        } else {
            return true;
        }
    }

    const isDigit = (evt) =>{
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode < 48 || charCode > 57) {
            evt.preventDefault();
        } else {
            return true;
        }
    }

    const formGenerate = (evt) => {
        document.getElementById("number-boosters").style.borderColor="#C2C5C6";
        form.weeks = [];
        form.weekdays = [];
        form.times = [];
        for(var i = 0; i < parseInt(form.number_of_boosters_month); i++){
            form.weeks[i]= "";
            form.weekdays[i]= "";
            form.times[i]= "";
        }
    }

    const uploadFile = (e) => {
        var fileName = e.target.files[0].name;
        var idxDot = fileName.lastIndexOf(".") + 1;
        var extFile = fileName.substr(idxDot, fileName.length).toLowerCase();
        if (extFile=="jpg" || extFile=="jpeg" || extFile=="png"){
            document.getElementById("filename-show").innerHTML=e.target.files[0].name;
            document.getElementById("remove").style.display = "block";
            form.file = e.target.files[0];
            form.file_name = e.target.files[0].name;
            let reader = new FileReader();
            reader.readAsDataURL(e.target.files[0]);
            reader.onload = e => {
                document.getElementById("img").src = e.target.result;
            }
        }else{
            modal.show(FieldMissing, {
                config: {
                    staticBackdrop: true,
                },
                props:{
                    title: '',
                    description:'Nur jpg/jpeg und png Dateien sind erlaubt.'
                }
            });
        }
    }

    const removeFile = () =>{
        form.file = '';
        form.file_name = '';
        document.getElementById("filename-show").innerHTML= "Bild hochladen";
        document.getElementById("remove-msg-show").innerHTML = "Bild entfernen";
    }

    const getContract = (e) => {
        let id = e.target.value;
        axios.get(route('boosters.get-contracts', {sales_partner_id: id}))
          .then((res) => {
             contracts.value = res.data.contract;
             form.contract = ""
             if(res.data.booster.length > 0){
                modal.show(BoosterList,{
                    config: {
                        staticBackdrop: true,
                    },
                    props:{
                        boosters: res.data.booster,
                        sales_partner: res.data.sales_partner
                    },
                    events: {
                    yesClick: () => {
                        modal.hide()
                    },
                    noClick: () => modal.hide(),
                },
                });
             }
        });
    }

    const isValidBoosterType = () =>{
        var flag=0;
        if(form.booster_type == 'Recurring'){
            if(parseInt(form.number_of_boosters_month) == 0){
                form_error['number_of_boosters_month']['error'] = true
                document.getElementById("number-boosters").style.borderColor="red"
                flag=1;
            }
            var els = document.getElementsByClassName("week");
            for(var i = 0; i < els.length; i++)
            {
                if(els[i].value == ""){
                    els[i].style.borderColor="red";
                    flag=1;
                }
            }
            els = document.getElementsByClassName("weekday");
            for(var i = 0; i < els.length; i++)
            {
                if(els[i].value == ""){
                    els[i].style.borderColor="red";
                    flag=1;
                }
            }
            els = document.getElementsByClassName("time");
            for(var i = 0; i < els.length; i++)
            {
                if(els[i].value == ""){
                    els[i].style.borderColor="red";
                    flag=1;
                }
            }
        }
        if(flag==1){
            return false
        }
        return true
    }

    const update = async () => {
        const isValidForBoosterType = isValidBoosterType();
        if(inviteable.value == false){
            Object.keys(rules).forEach( (terms) =>
                {
                    form_error[terms]['error'] = false;
                }
            );

            v$.value.$silentErrors.forEach( (terms) =>
                {
                    form_error[terms.$property]['error'] = true;
                }
            );

            if ( v$.value.$invalid ) {
                modal.show(FieldMissing,{
                    config: {
                        staticBackdrop: true,
                    },
                });
            }

            return !v$.value.$invalid;
        }
        else if(!isValidForBoosterType){
            return;
        }
        else if((inviteable.value == true && isValidForBoosterType == true) && form.title.length < 41 || (form.body != null && form.body.length < 251)){
            form.post(route("boosters.update", {
                booster: props.booster.id,
            }), {
                forcedData: true,

                onSuccess: () => {
                    form.reset();
                },
            })
        }
    }

    if(form.booster_type == 'Recurring'){
        for(var i = 0; i < parseInt(form.number_of_boosters_month); i++){
            weeks[i]= props.booster.booster_booster_types[i].week;
            weekdays[i]= props.booster.booster_booster_types[i].weekday;
            var time_format = props.booster.booster_booster_types[i].time.split(':');
            times[i]= time_format[0]+":"+time_format[1];
        }
    }

    const validateHhMm = (evt, id) => {
        var isValid = /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(evt.target.value);
        if (isValid) {
            document.getElementById(id).style.borderColor = '';
        } else {
            document.getElementById(id).style.borderColor = 'red';
        }
        return isValid;

    }
</script>

