interface CategorySearch {
  id: number,
  name: string,
}

interface ProductSearch {
  id: number,
  name: string,
  description: string,
  price: string,
  path: string | null,
}

interface Search {
  categories: Array<CategorySearch>,
  products: Array<ProductSearch>
}

//------------------- SEARCH -------------------//
const searchDiv: HTMLDivElement = document.querySelector('#search > div');
const inputSearch: HTMLInputElement = searchDiv.querySelector<HTMLInputElement>('input#search-bar');
const ulSearch: HTMLUListElement = searchDiv.querySelector<HTMLUListElement>('ul');
const overlay: HTMLDivElement = document.querySelector<HTMLDivElement>('div#searching-overlay');

const fetchSearch = async (q: string): Promise<Search> => {
  const res: Response = await fetch(`/api/search?q=${encodeURIComponent(q)}`);
  return await res.json();
};

inputSearch.addEventListener('input', async () => {
    const value: string = inputSearch.value.trim();
    if (!value) {
      overlay.style.display = 'none';
      ulSearch.replaceChildren();
      return;
    }
    overlay.style.display = 'block';

    const data: Search = await fetchSearch(value);
    ulSearch.replaceChildren();

    const bold = (s: string): [HTMLElement, HTMLElement, HTMLElement] => {
      const strong = document.createElement('strong');
      strong.textContent = value.toLowerCase();

      const i = s.toLowerCase().indexOf(value.toLowerCase());

      const span1 = document.createElement('span');
      span1.textContent = i >= 0 ? s.substring(0, i) : s;

      if (i < 0) return [span1, null, null];

      const span2 = document.createElement('span');
      span2.textContent = s.substring(i + value.length);
      return [span1, strong, span2];
    };

    data.categories.forEach((category: CategorySearch) => {
      const li: HTMLLIElement = document.createElement<'li'>('li');
      const a: HTMLAnchorElement = document.createElement<'a'>('a');

      a.href = `/products/${category.id}`;

      const div: HTMLDivElement = document.createElement<'div'>('div');
      div.append(...bold(category.name).filter(f => f != null));
      const div2: HTMLDivElement = document.createElement<'div'>('div');

      a.appendChild(div);
      a.appendChild(div2);
      li.appendChild(a);

      ulSearch.appendChild<HTMLLIElement>(li);
    });

    data.products.forEach((article: ProductSearch) => {
      const li: HTMLLIElement = document.createElement<'li'>('li');
      const a: HTMLAnchorElement = document.createElement<'a'>('a');

      a.href = `/product/${article.id}`;

      const div: HTMLDivElement = document.createElement<'div'>('div');
      div.append(...bold(article.name).filter(f => f != null));
      const div2: HTMLDivElement = document.createElement<'div'>('div');
      if (article.path) {
        div2.classList.add('image');

        const img: HTMLImageElement = document.createElement<'img'>('img');
        img.src = `/images/product/${article.path}`;
        img.alt = article.name;
        div2.appendChild(img);
      }
      a.appendChild(div);
      a.appendChild(div2);
      li.appendChild(a);

      ulSearch.appendChild<HTMLLIElement>(li);
    });
  }
);
const setActive = (b: boolean) => {
  if (b) {
    searchDiv.classList.add('active');
    if (inputSearch.value.trim() !== '')
      overlay.style.display = 'block';
  } else {
    searchDiv.classList.remove('active');
    overlay.style.display = 'none';
  }
};

inputSearch.addEventListener('focusin', (): void => {
  setActive(true);
});

document.addEventListener('focusin', (e: FocusEvent) => {
  setActive(searchDiv.contains(e.target as Node));
});

overlay.addEventListener('click', (): void => {
  setActive(false);
});
