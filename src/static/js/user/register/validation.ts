interface InputFieldObject {
  inputField: HTMLInputElement,
  errorSpan: HTMLSpanElement,
  validator: (inputField: HTMLInputElement) => string | void
}

const inRange = (length: number, min: number, max: number): boolean => min <= length && length <= max;

/**
 * @see https://regexr.com/3e48o
 */
const emailValidationRegex = /^[\w-.]+@([\w-]+\.)+[\w-]{2,4}$/;

const registerForm = document.querySelector<HTMLFormElement>('#register-form');
const passwordInputField = document.querySelector<HTMLInputElement>('#password');

const inputFields: InputFieldObject[] = [
  {
    inputField: document.querySelector<HTMLInputElement>('#firstname'),
    errorSpan: document.querySelector<HTMLInputElement>('#firstname-error'),
    validator: (inputField) => {
      if (!inRange(inputField.value.trim().length, 1, 64)) return 'First name must be between 1 and 64 characters long';
    }
  },
  {
    inputField: document.querySelector<HTMLInputElement>('#lastname'),
    errorSpan: document.querySelector<HTMLInputElement>('#lastname-error'),
    validator: (inputField) => {
      if (!inRange(inputField.value.trim().length, 1, 64)) return 'Last name must be between 1 and 64 characters long';
    }
  },
  {
    inputField: document.querySelector<HTMLInputElement>('#email'),
    errorSpan: document.querySelector<HTMLInputElement>('#email-error'),
    validator: (inputField) => {
      if (!inputField.value.match(emailValidationRegex)) return 'Please fill in a valid email address';
    }
  },
  {
    inputField: passwordInputField,
    errorSpan: document.querySelector<HTMLInputElement>('#password-error'),
    validator: (inputField) => {
      if (!inRange(inputField.value.length, 8, 32))
        return 'Password must be between 8 and 32 characters long';
      if (!inputField.value.match(/[a-z]/))
        return 'Password must contain at least 1 lower-case character';
      if (!inputField.value.match(/[A-Z]/))
        return 'Password must contain at least 1 upper-case character';
      if (!inputField.value.match(/\d/))
        return 'Password must contain at least 1 digit';
    }
  },
  {
    inputField: document.querySelector<HTMLInputElement>('#password-confirm'),
    errorSpan: document.querySelector<HTMLInputElement>('#password-confirm-error'),
    validator: (inputField) => {
      if (inputField.value !== passwordInputField.value)
        return 'Confirm password must equal password';
    }
  },
];

const validate = (inputValue: InputFieldObject): boolean => {
  const value = inputValue.validator(inputValue.inputField);

  if (value) {
    inputValue.errorSpan.textContent = value;
    return true;
  } else {
    inputValue.errorSpan.textContent = '';
    return false;
  }
};

inputFields.forEach(inputValue => inputValue.inputField.addEventListener('input', () => validate(inputValue)));

registerForm.addEventListener('submit', (e) => {
  let prevent = false;
  inputFields.forEach((inputValue: InputFieldObject) => {
    if (validate(inputValue))
      prevent = true;
  });

  if (prevent)
    e.preventDefault();
});
