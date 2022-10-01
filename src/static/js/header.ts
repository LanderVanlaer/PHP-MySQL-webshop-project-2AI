interface ICategorySearch {
  id: number,
  nameD: string,
  nameF: string,
  nameE: string,
}

interface IArticleSearch {
  id: number,
  name: string,
  path: string,
}

interface ISearch {
  categories: Array<ICategorySearch>,
  articles: Array<IArticleSearch>
}

//------------------- SEARCH -------------------//
const searchDiv: HTMLDivElement = document.querySelector('#search > div');
const inputSearch: HTMLInputElement = searchDiv.querySelector<HTMLInputElement>('input#search-bar');
const ulSearch: HTMLUListElement = searchDiv.querySelector<HTMLUListElement>('ul');
const overlay: HTMLDivElement = document.querySelector<HTMLDivElement>('div#searching-overlay');

const fetchSearch = async (q: string): Promise<ISearch> => {
  const res: Response = await fetch(`/api/search.php?q=${q}`);
  return await res.json();
};

const getLanguage = (): string => {
  const lReg = document.cookie.match(/lang=([dfe])/);
  return lReg ? lReg[1] : 'e';
};

inputSearch.addEventListener('input', async () => {
    const value: string = inputSearch.value.trim();
    if (!value) {
      overlay.style.display = 'none';
      return ulSearch.innerHTML = null;
    }
    overlay.style.display = 'block';

    const bold: (s: string) => string = (s: string) => s.replaceAll(new RegExp(`(.*)(${value})(.*)`, 'gi'), '$1<b>$2</b>$3');

    const data: ISearch = await fetchSearch(value);
    ulSearch.innerHTML = null;

    data.categories.forEach((category: ICategorySearch) => {
      const li: HTMLLIElement = document.createElement<'li'>('li');
      li.innerHTML = `<a href="/articles/${category.id}"><div>${bold(category[`name${getLanguage().toUpperCase()}`])}</div><div></div></a>`;
      ulSearch.appendChild<HTMLLIElement>(li);
    });

    data.articles.forEach((article: IArticleSearch) => {
      const li: HTMLLIElement = document.createElement<'li'>('li');
      li.innerHTML = `<a href="/article/${article.id}"><div>${bold(article.name)}</div><div class="image"><img src="/images/articles/${article.path}" alt="${article.name}"></div></a>`;
      ulSearch.appendChild<HTMLLIElement>(li);
    });
  }
);
const setActive = (b: boolean) => {
  if (b) {
    searchDiv.classList.add('active');
    if (inputSearch.value.trim())
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
