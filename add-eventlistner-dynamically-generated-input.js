// document.getElementById("birth_date").addEventListener("blur", handleAgeCheck);

// add event listner dynamically generated input by JavaScript
document.addEventListener("blur", function (e) {
    if (e.target && e.target.id === "birth_date") {
        console.log("Child Age field blurred");
        handleAgeCheck(e.target);
    }
}, true);

var is_new_form_opened = false;
function handleAgeCheck(field) {
    // Replace with your link
    const formURL = "https://f1mate.com";

    let val = field?.value || '';
    val = val.trim();

    if (!val || val == '' || val == undefined || val == null) {
        return;
    }

    // if the input fileld is text or number (age input)
    if (/^\d+$/.test(val)) {
        const age = parseInt(val, 10);
        if (age > 17 && !is_new_form_opened) {
            is_new_form_opened = true;
            window.open(formURL, "_blank");
        }

        if (is_new_form_opened) {
            setTimeout(() => {
                is_new_form_opened = false;
            }, (1000 * 60 * 1));
        }
        return;
    }

    // if the input filed is the date (DOB)
    if (/^\d{4}-\d{2}-\d{2}$/.test(val)) {
        const today = new Date();
        const birth = new Date(val);

        if (isNaN(birth.getTime())) {
            return;
        }

        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        if (age > 17 && !is_new_form_opened) {
            is_new_form_opened = true;
            window.open(formURL, "_blank");
        }

        if (is_new_form_opened) {
            setTimeout(() => {
                is_new_form_opened = false;
            }, (1000 * 60 * 2));
        }
        return;
    }
}