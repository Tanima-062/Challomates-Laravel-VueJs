<template>
        <div class="qr__code fixed px-[57px] w-[428px] h-[571px] py-6 flex justify-center items-center flex-col bg-white z-10 border-[1px] border-secondary-2 rounded-[24px] top-[50%] left-[50%] translate-x-[-50%] translate-y-[-50%]" v-click-away="() => this.$emit('close')">
            <div class="qr__code--header flex justify-center mb-5">
                <qr-code />
            </div>
            <div class="qr__code--body flex justify-center mb-5" id="qr__code--body">
                <qrcode-vue :value="text.toString()" :size="295" class="qr__code__preview" id="qr__code__preview"
                    ref="qr__code__preview" render-as="svg" />
            </div>
            <div class="qr__code--footer w-[264px] flex flex-col gap-4 justify-center items-center">
                <button
                    class="flex items-center justify-center text-primary-1 border-[1px] w-full h-[40px] border-primary-1 rounded-[37px] font-poppins text-15"
                    @click="print">
                    QR Code drucken
                </button>
                <button
                    class="flex items-center justify-center text-primary-1 border-[1px] w-full h-[40px] border-primary-1 rounded-[37px] font-poppins text-15"
                    @click="download">
                    QR Code herunterladen
                </button>
                <!-- <a
                    target="_blank"
                    href="mailto:"
                    class="flex items-center justify-center text-primary-1 border-[1px] w-full h-[40px] border-primary-1 rounded-[37px] font-poppins text-15">
                    QR Code per E-Mail senden
                </a> -->
                <button
                    class="flex items-center justify-center text-primary-1 border-[1px] w-full h-[40px] border-primary-1 rounded-[37px] font-poppins text-15"
                    @click="sendEmail"
                >
                    QR Code per E-Mail senden
                </button>
            </div>
        </div>
</template>

<script>
import QrCode from "../../Components/Icons/QrCode.vue";
import QrcodeVue from "qrcode.vue";
import axios from "axios";
import FlashNotification from './../Modal/Content/FlashNotification.vue'

export default {
    props: {
        text: {
            type: [String, Number],
            required: true,
        },
    },
    inject: ['modal'],

    components: {
        QrCode,
        QrcodeVue,
    },
    methods: {
        download() {
            const svg = new XMLSerializer().serializeToString(
                document.getElementById("qr__code__preview")
            );
            const qrCodeData = `data:image/svg+xml;base64,${window.btoa(svg)}`;

            const canvas = document.createElement("canvas"),
                context = canvas.getContext("2d");
            canvas.height = "295";
            canvas.width = "295";

            const image = new Image();
            image.src = qrCodeData;
            image.onload = function () {
                context.drawImage(image, 0, 0);
                const a = document.createElement("a");
                a.download = "product.png";
                a.href = canvas
                    .toDataURL("image/png")
                    .replace("image/png", "image/octet-stream");
                a.click();
            };

            this.$emit("close");
        },

        // print() {
        //     this.$emit("close");
        //     let canvas = document.getElementById("qr__code--body").innerHTML;
        //     canvas = `<html>\n<head></head><body>${canvas}</body>\n</html>`
        //     setTimeout(() => {
        //         let w = window.open('', 'PrintWindow', 'toolbars=no,scrollbars=yes,status=no,resizable=yes');
        //         w.document.write(canvas);
        //         w.print();
        //         w.close();
        //     }, 200)

        // },
        print() {
            let canvas = document.getElementById("qr__code--body").innerHTML;
            const w = window.open();
            const originalContents = document.body.innerHTML;
            setTimeout(()=> {
                w.document.body.innerHTML = canvas;
                w.print();
                w.close();
                w.onafterprint = () => w.close();
            }, 500)
        },

        async sendEmail(){
            const svg = new XMLSerializer().serializeToString(
                document.getElementById("qr__code__preview")
            );
            const qrCodeData = `data:image/svg+xml;base64,${window.btoa(svg)}`;
            // const qrCodeData = `data:image/png;base64,${window.btoa(svg)}`;

            const data = await this.base64SvgToBase64Png(qrCodeData, 200)

            // console.log(data)



            // console.log(qrCodeData)
            axios.post(route('sales-partner.email-qr-code'), {
                'image': data,
                'sales_partner_id':  JSON.parse(this.text).company_id
            })
            .then(res=>{
                // console.log(res.data)
                this.$emit("close");
                this.modal.show(FlashNotification, {
                    props: {
                        title: "Benachrichtigung",
                        message: "Der QR-Code wurde gesendet!",
                    },
                })

                setTimeout(()=> {
                    this.modal.hide()
                }, 3000)

            })
            .catch(err=>console.log(err))
        },
        async base64SvgToBase64Png (originalBase64, width) {
            return new Promise(resolve => {
                let img = document.createElement('img');
                img.onload = function () {
                    document.body.appendChild(img);
                    let canvas = document.createElement("canvas");
                    let ratio = (img.clientWidth / img.clientHeight) || 1;
                    document.body.removeChild(img);
                    canvas.width = width;
                    canvas.height = width / ratio;
                    let ctx = canvas.getContext("2d");
                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    try {
                        let data = canvas.toDataURL('image/png');
                        resolve(data);
                    } catch (e) {
                        resolve(null);
                    }
                };
                img.onerror = function() {
                    resolve(null);
                };
                img.src = originalBase64;
            });
        }
    },
};
</script>

<style lang="scss" scoped>
</style>
