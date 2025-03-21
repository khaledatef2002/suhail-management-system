import { request } from './utils.js'
const page_lang = document.querySelector("html").getAttribute("lang")
const lang = await import("../libs/intl-tel-input/js/i18n/" + page_lang + "/index.js")

let iti = null

if(document.querySelector("input.country-selector"))
{
    iti = window.intlTelInput(document.querySelector("input.country-selector"), {
        i18n: lang,
        initialCountry: "auto",
        geoIpLookup: callback => {
            fetch("https://ipapi.co/json")
                .then(res => res.json())
                .then(data => callback(data.country_code))
                .catch(() => callback("UAE"));
        },
        strictMode: true,
        separateDialCode: true,
        allowDropdown: true,
        autoPlaceholder: "aggressive",
        hiddenInput: (telInputName) => ({
            phone: "phone_numer",
            country: "country_code"
        }),
        loadUtils: () => import("../libs/intl-tel-input/js/utils.js"),
    });
}

const enable_internship_form = function()
{
    document.querySelector("form#apply-internship")?.addEventListener('submit', async function(e){
        e.preventDefault()

        const form = this
        const formData = new FormData(form)

        formData.set("phone_number", iti.getNumber())
        formData.set("country_code", iti.getSelectedCountryData().iso2.toUpperCase())
        
        const submit_button = form.querySelector("button[type='submit']")
        submit_button.disabled = true

        const response = await request("/internship-apply", "POST", formData)
        
        if(response.success) {
            form.querySelectorAll("input:not([name='_token']").forEach(input => input.value = "")
            Swal.fire({
                text: response.data.message,
                icon: "success",
                confirmButtonText: __.ok,
            })
        }
        else
        {
            Swal.fire({
                text: response.message,
                icon: "error",
                confirmButtonText: __.ok,
            })
        }
    
        submit_button.disabled = false
    })
}

function init()
{
    enable_internship_form()
}

init()