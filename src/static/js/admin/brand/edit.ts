const typeRadioInputs: RadioNodeList = document.forms['brand-edit'].elements.type;
const divUpload: HTMLDivElement = document.getElementById('logo-type-choose-upload-div') as HTMLDivElement;
const divExisting: HTMLDivElement = document.getElementById('logo-type-choose-existing-div') as HTMLDivElement;

typeRadioInputs.forEach(i =>
  i.addEventListener('change', () => {
    if (typeRadioInputs.value === 'upload') {
      divUpload.style.display = 'block';
      divExisting.style.display = 'none';
    } else {
      divUpload.style.display = 'none';
      divExisting.style.display = 'block';
    }
  }));

const logoImageRadioInputs: RadioNodeList = document.forms['brand-edit'].elements['logo-image'];
const imgLogoPreview: HTMLImageElement = document.getElementById('preview') as HTMLImageElement;

logoImageRadioInputs.forEach(i =>
  i.addEventListener('change', () => {
    imgLogoPreview.src = `/images/brand/${logoImageRadioInputs.value}`;
  }));
