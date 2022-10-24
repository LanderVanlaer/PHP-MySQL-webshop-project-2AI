const template = document.getElementById('specification-template') as HTMLTemplateElement;
const tableBody = document.querySelector<HTMLTableSectionElement>('#specifications tbody');
let currentId = 0;

const EXAMPLE_DATA = {
  string: 'xxx',
  number: 20,
};

const configureSpecificationNotation = (id: number,
                                        exampleNotationPTag: HTMLParagraphElement,
                                        typeInput: HTMLSelectElement,
                                        notationInput: HTMLInputElement) => {
  [notationInput, typeInput]
    .forEach(el => el.addEventListener('input', () => {
      const value = notationInput.value;
      exampleNotationPTag.textContent = EXAMPLE_DATA[typeInput.value] && value.trim() ? value.replace('{}', EXAMPLE_DATA[typeInput.value]) : '';
    }));
};

const configureDeleteButton = (btn: HTMLButtonElement, id: number) => {
  btn.addEventListener('click', () => {
    confirm('Are you sure, you want to delete this row?') && document.querySelector(`#specifications tr:has([id="specification-name[${id}]"])`).remove();
  });
};

// ------------------------------------
configureSpecificationNotation(0,
  document.getElementById('specification-notation-example[0]') as HTMLParagraphElement,
  document.getElementById('specification-type[0]') as HTMLSelectElement,
  document.getElementById('specification-notation[0]') as HTMLInputElement
);
configureDeleteButton(document.getElementById('delete-0') as HTMLButtonElement, 0);

const buttonAddRow = document.getElementById('add-row') as HTMLButtonElement;
buttonAddRow.addEventListener('click', e => {
  e.preventDefault();

  ++currentId;

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

  configureSpecificationNotation(currentId, exampleNotationPTag, typeInput, notationInput);
  configureDeleteButton(tr.querySelector<HTMLButtonElement>('button.delete'), currentId);

  tableBody.appendChild(tr);
});
