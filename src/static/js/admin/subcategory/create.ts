const template = document.getElementById('specification-template') as HTMLTemplateElement;
const tableBody = document.querySelector<HTMLTableSectionElement>('#specifications tbody');
let currentId = -1;

const EXAMPLE_DATA = {
  string: 'xxx',
  number: 20,
};

const newCurrentId = () => {
  let newId = --currentId;
  while (document.querySelector(`[id="specification-name[${newId}]"]`)) --newId;
  currentId = newId;
  console.log(currentId);
};

const configureSpecificationNotation = (exampleNotationPTag: HTMLParagraphElement, typeInput: HTMLSelectElement, notationInput: HTMLInputElement) => {
  const renderNotationExample = () => {
    const value = notationInput.value;
    exampleNotationPTag.textContent = EXAMPLE_DATA[typeInput.value] && value.trim() ? value.replace('{}', EXAMPLE_DATA[typeInput.value]) : '';
  };

  renderNotationExample();

  [notationInput, typeInput].forEach(el => el.addEventListener('input', renderNotationExample));
};

const configureDeleteButton = (btn: HTMLButtonElement, id: number) => {
  btn.addEventListener('click', () => {
    confirm('Are you sure, you want to delete this row?') && document.querySelector(`#specifications tr:has([id="specification-name[${id}]"])`).remove();
  });
};

// ------------------------------------
// Config notation's
document.querySelectorAll<HTMLSelectElement>('#specifications tbody tr td select').forEach(el => {
  const id = Number(/^specification-type\[(-?\d+)]$/i.exec(el.id)[1]);

  if (isNaN(id)) return alert('Something went wrong, please refresh your page');

  configureSpecificationNotation(
    document.getElementById(`specification-notation-example[${id}]`) as HTMLParagraphElement,
    el,
    document.getElementById(`specification-notation[${id}]`) as HTMLInputElement
  );
});

// Configure Delete buttons
document.querySelectorAll<HTMLButtonElement>('#specifications > tbody > tr > td.edit > button').forEach(btn => {
  if (btn.dataset.id && !isNaN(Number(btn.dataset.id)))
    configureDeleteButton(btn, Number(btn.dataset.id));
});

// "Add Row" button config
const buttonAddRow = document.getElementById('add-row') as HTMLButtonElement;
buttonAddRow.addEventListener('click', e => {
  e.preventDefault();

  newCurrentId();

  const tr = template.content.cloneNode(true) as HTMLTableRowElement;

  const exampleNotationPTag = tr.querySelector<HTMLParagraphElement>('#specification-notation-example');
  const typeInput = tr.querySelector<HTMLSelectElement>('#specification-type');
  const notationInput = tr.querySelector<HTMLInputElement>('#specification-notation');

  [
    tr.querySelector<HTMLInputElement>('#specification-name'),
    exampleNotationPTag,
    typeInput,
    notationInput,
  ].forEach(element => {
    element.id = `${element.id}[${currentId}]`;
    'name' in element && (element.name = `${element.name}[${currentId}]`);
  });

  configureSpecificationNotation(exampleNotationPTag, typeInput, notationInput);
  configureDeleteButton(tr.querySelector<HTMLButtonElement>('button.delete'), currentId);

  tableBody.appendChild(tr);
});
