declare const subcategories: { id: number, name: string, selected?: boolean }[];

const searchBar = document.querySelector<HTMLInputElement>('#subcategories-search');
const searchList = document.querySelector<HTMLUListElement>('#subcategories-search-list');
const selectedList = document.querySelector<HTMLUListElement>('#subcategories-selected');
const selectedListInputs = document.querySelector<HTMLDivElement>('#subcategories-hidden');

const bold = (s: string, v: string): [HTMLElement, HTMLElement, HTMLElement] => {
  const strong = document.createElement('strong');
  strong.textContent = v;

  const i = s.toLowerCase().indexOf(v);

  const span1 = document.createElement('span');
  span1.textContent = i >= 0 ? s.substring(0, i) : s;

  if (i < 0) return [span1, null, null];

  const span2 = document.createElement('span');
  span2.textContent = s.substring(i + v.length);
  return [span1, strong, span2];
};

const addToSelected = (id: number) => {
  if ([...selectedList.querySelectorAll<HTMLButtonElement>('li button')]
    .some(b => Number(b.dataset.subcategoryId) === id)
  ) return;

  const button = document.createElement('button');
  button.textContent = subcategories.find(s => s.id == id).name;
  button.dataset.subcategoryId = String(id);
  button.type = 'button';

  const li = document.createElement('li');
  li.appendChild(button);
  selectedList.appendChild(li);

  const input = document.createElement('input');
  input.type = 'hidden';
  input.value = String(id);
  input.name = 'subcategories[]';
  selectedListInputs.appendChild(input);

  button.addEventListener('click', () => {
    if (!confirm('Are you sure, you want to delete this Subcategory from the category')) return;
    input.remove();
    li.remove();
    button.remove();
  });
};


const showSubcategories = () => {
  const s = searchBar.value.trim().toLowerCase();

  const lis: HTMLLIElement[] = [];
  subcategories.forEach(sc => {
    if (!s || sc.name.toLowerCase().includes(s) || String(sc.id).includes(s)) {
      const li = document.createElement('li');
      const button = document.createElement('button');

      button.addEventListener('click', () => {
        addToSelected(sc.id);
      });

      button.type = 'button';
      const idSpan = document.createElement('span');
      idSpan.textContent = `(${sc.id}) `;
      button.append(idSpan);
      button.append(...bold(sc.name, s).filter(f => f != null));
      li.appendChild(button);
      lis.push(li);
    }
  });

  searchList.replaceChildren(...lis);
};

searchBar.addEventListener('input', showSubcategories);

showSubcategories();

subcategories.forEach(s => {
  if (s.selected) addToSelected(s.id);
});
