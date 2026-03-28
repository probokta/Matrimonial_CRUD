const form = document.getElementById("biodataForm");
const fields = Array.from(form.querySelectorAll("input, select, textarea"));
const imageInput = document.getElementById("photoUpload");
const birthDateInput = document.getElementById("birthDate");

const requiredMessages = {
    fullName: "Please enter your full name.",
    birthDate: "Please select your birth date.",
    birthTime: "Please select your birth time.",
    birthPlace: "Please enter your birth place.",
    religion: "Please enter your religion.",
    caste: "Please enter your caste.",
    height: "Please enter your height.",
    bloodGroup: "Please select your blood group.",
    education: "Please enter your education.",
    occupation: "Please enter your occupation.",
    fatherName: "Please enter your father name.",
    fatherOccupation: "Please enter your father occupation.",
    motherName: "Please enter your mother name.",
    sisters: "Please enter number of sisters.",
    brothers: "Please enter number of brothers.",
    contact: "Please enter your contact number.",
    address: "Please enter your address."
};

const validators = {
    fullName: {
        test: (value) => /^[A-Za-z][A-Za-z ]{2,49}$/.test(value),
        message: "Name must be 3 to 50 letters and spaces only."
    },
    birthDate: {
        test: (value) => {
            if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) return false;
            const enteredDate = new Date(`${value}T00:00:00`);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return enteredDate <= today && enteredDate.getFullYear() >= 1900;
        },
        message: "Birthdate must be a valid past date (year 1900 or later)."
    },
    birthTime: {
        test: (value) => /^([01]\d|2[0-3]):([0-5]\d)$/.test(value),
        message: "Birth time must be in 24-hour HH:MM format."
    },
    birthPlace: {
        test: (value) => value.length >= 2,
        message: "Birth place is required."
    },
    religion: {
        test: (value) => value.length >= 2,
        message: "Religion is required."
    },
    caste: {
        test: (value) => value.length >= 2,
        message: "Caste is required."
    },
    height: {
        test: (value) => /^([3-7])\s*ft\s*([0-9]|1[01])\s*in$/i.test(value),
        message: "Height must be like: 5 ft 6 in."
    },
    bloodGroup: {
        test: (value) => value !== "",
        message: "Please select a blood group."
    },
    education: {
        test: (value) => value.length >= 2,
        message: "Education is required."
    },
    occupation: {
        test: (value) => value.length >= 2,
        message: "Occupation is required."
    },
    fatherName: {
        test: (value) => value.length >= 2,
        message: "Father name is required."
    },
    fatherOccupation: {
        test: (value) => value.length >= 2,
        message: "Father occupation is required."
    },
    motherName: {
        test: (value) => value.length >= 2,
        message: "Mother name is required."
    },
    sisters: {
        test: (value) => /^(\d+)(\s*\(\s*\d+\s*Married\s*\))?$/i.test(value),
        message: "Sisters must be like 1 or 1 (0 Married)."
    },
    brothers: {
        test: (value) => /^(\d+)(\s*\(\s*\d+\s*Married\s*\))?$/i.test(value),
        message: "Brothers must be like 2 or 2 (1 Married)."
    },
    contact: {
        test: (value) => /^(?:\+?88)?01[3-9]\d{8}$/.test(value),
        message: "Contact number must be valid (e.g. 01900000000)."
    },
    address: {
        test: (value) => value.length >= 10,
        message: "Address must be at least 10 characters."
    }
};

function getErrorElement(field) {
    let errorEl = field.nextElementSibling;
    if (!errorEl || !errorEl.classList.contains("error-message")) {
        errorEl = document.createElement("div");
        errorEl.className = "error-message";
        field.insertAdjacentElement("afterend", errorEl);
    }
    return errorEl;
}

function setFieldState(field, valid, message = "") {
    const errorEl = getErrorElement(field);
    field.classList.toggle("input-error", !valid);
    field.classList.toggle("input-valid", valid);
    field.setAttribute("aria-invalid", String(!valid));
    errorEl.textContent = valid ? "" : message;
}

function validateField(field) {
    if (field.type === "file") return true;

    const value = field.value.trim();
    if (field.required && value === "") {
        setFieldState(field, false, requiredMessages[field.id] || "This field is required.");
        return false;
    }

    const rule = validators[field.id];
    if (rule && !rule.test(value)) {
        setFieldState(field, false, rule.message);
        return false;
    }

    setFieldState(field, true);
    return true;
}

function formatBirthDateInput(value) {
    const digitsOnly = value.replace(/\D/g, "").slice(0, 8);
    if (digitsOnly.length <= 4) return digitsOnly;
    if (digitsOnly.length <= 6) return `${digitsOnly.slice(0, 4)}-${digitsOnly.slice(4)}`;
    return `${digitsOnly.slice(0, 4)}-${digitsOnly.slice(4, 6)}-${digitsOnly.slice(6, 8)}`;
}

fields.forEach((field) => {
    if (field.type === "file") return;
    const eventName = field.tagName === "SELECT" ? "change" : "input";
    field.addEventListener(eventName, () => {
        if (field.id === "birthDate") {
            field.value = formatBirthDateInput(field.value);
        }
        validateField(field);
    });
    field.addEventListener("blur", () => validateField(field));
});

if (birthDateInput) {
    birthDateInput.addEventListener("paste", () => {
        requestAnimationFrame(() => {
            birthDateInput.value = formatBirthDateInput(birthDateInput.value);
            validateField(birthDateInput);
        });
    });
}

// Modified submit handler: validate first, then allow form submission if valid
form.addEventListener("submit", (event) => {
    const allValid = fields
        .filter((field) => field.type !== "file")
        .every((field) => validateField(field));

    if (!allValid) {
        event.preventDefault();
        const firstInvalid = form.querySelector(".input-error");
        if (firstInvalid) firstInvalid.focus();
    } else {
        // If valid, let the form submit normally (no preventDefault)
        return true;
    }
});

imageInput.addEventListener("change", (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (!file.type.startsWith("image/")) {
        alert("Please upload a valid image file.");
        imageInput.value = "";
        return;
    }

    const preview = document.getElementById("preview");
    preview.src = URL.createObjectURL(file);
});