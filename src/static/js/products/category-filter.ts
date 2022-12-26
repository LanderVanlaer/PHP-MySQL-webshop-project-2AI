enum FilterType {
  STRING = 'string',
  BOOLEAN = 'boolean',
  LIST = 'list',
  NUMBER = 'number',
}

interface Product {
  brand: number;
  specifications: Record<number, string>;
  element: HTMLLIElement,

  show(s: boolean),
}

class Filter {
  public readonly specificationId: number;
  private readonly type: FilterType;
  private readonly booleanCheckbox?: HTMLInputElement;
  private readonly listItems?: HTMLInputElement[];
  private readonly numberInputs?: {
    min: HTMLInputElement,
    max: HTMLInputElement,
  };
  private readonly defaultNumberInputValues?: {
    min: number,
    max: number,
  };

  constructor(specificationListItem: HTMLLIElement) {
    this.specificationId = Number(specificationListItem.dataset.specificationId);
    this.type = specificationListItem.dataset.specificationType as FilterType;

    this.booleanCheckbox = specificationListItem.querySelector<HTMLInputElement>('input[type="checkbox"]');
    this.listItems = [...specificationListItem.querySelectorAll<HTMLInputElement>('ul > li input[type="checkbox"]')];

    this.numberInputs = {
      min: specificationListItem.querySelector<HTMLInputElement>('input.min[type="number"]'),
      max: specificationListItem.querySelector<HTMLInputElement>('input.max[type="number"]'),
    };

    if (this.numberInputs.min && this.numberInputs.max)
      this.defaultNumberInputValues = {
        min: Number(this.numberInputs.min.value),
        max: Number(this.numberInputs.max.value),
      };

    [this.numberInputs.min, this.numberInputs.max, this.booleanCheckbox, ...this.listItems].forEach(element => {
      if (!element) return;

      element.addEventListener('change', (e) => {
        e.preventDefault();
        filter();
        updateSearchParamsInUrl();
      });
    });
  }

  public isOneChecked(): boolean {
    switch (this.type) {
      case FilterType.BOOLEAN:
        return this.booleanCheckbox.checked;
      case FilterType.NUMBER:
        return Number(this.numberInputs.min.value) !== this.defaultNumberInputValues.min || Number(this.numberInputs.max.value) !== this.defaultNumberInputValues.max;
      case FilterType.LIST:
      case FilterType.STRING:
        return this.listItems.some(li => li.checked);
    }
  }

  public applyParam(values: string[]): void {
    switch (this.type) {
      case FilterType.BOOLEAN:
        this.booleanCheckbox.checked = !!values.length;
        break;
      case FilterType.NUMBER:
        if (values.length < 2) throw new TypeError('The given value-array needs to have a length of at least 2');
        this.numberInputs.min.value = decodeURIComponent(values[0]);
        this.numberInputs.max.value = decodeURIComponent(values[1]);
        break;
      case FilterType.LIST:
      case FilterType.STRING:
        this.listItems.forEach(input => input.checked = values.some(v => input.value === decodeURIComponent(v)));
        break;
    }
  }

  public testProduct(product: Product): boolean {
    const value: string = product.specifications[this.specificationId];

    if (!this.isOneChecked()) return true;

    switch (this.type) {
      case FilterType.STRING:
      case FilterType.LIST:
        return this.listItems.some(li => {
          return li.checked && li.value === value;
        });
      case FilterType.BOOLEAN:
        return value === '1';
      case FilterType.NUMBER:
        const numberValue = Number(value);
        return Number(this.numberInputs.min.value) <= numberValue && numberValue <= Number(this.numberInputs.max.value);
      default:
        throw new TypeError(`Filter has a invalid type, ${this.type} is invalid`);
    }
  }

  /**
   * `#` is the delimiter
   */
  public toUrlParam(): [number, string] | null {
    if (!this.isOneChecked()) return null;

    switch (this.type) {
      case FilterType.STRING:
      case FilterType.LIST:
        return [this.specificationId, this.listItems.reduce((v: string, li: HTMLInputElement) => {
          if (!li.checked)
            return v;

          if (v.length === 0)
            return encodeURIComponent(li.value);

          return v + '#' + encodeURIComponent(li.value);
        }, '')];
      case FilterType.BOOLEAN:
        return [this.specificationId, 'on'];
      case FilterType.NUMBER:
        return [this.specificationId, `${this.numberInputs.min.value}#${this.numberInputs.max.value}`];
      default:
        throw new TypeError(`Filter has a invalid type, ${this.type} is invalid`);
    }
  }
}

const products: Product[] =
  [...document.querySelectorAll<HTMLLIElement>('#products > ul > li[data-brand-id][data-specifications]')]
    .map(li => ({
      brand: Number(li.dataset.brandId),
      specifications: JSON.parse(li.dataset.specifications),
      element: li,
      show(s: boolean) {
        li.style.display = s ? 'block' : 'none';
      }
    }));

const brandSpecification: HTMLInputElement[] =
  [...document.querySelectorAll<HTMLInputElement>('#brands input[type="checkbox"][name="brand"]')];

const activeBrandFilters: Record<number, boolean> = {};

const filters: Filter[] = [...document.querySelectorAll<HTMLLIElement>('#specifications li[data-subcategory-id] li[data-specification-id]')]
  .map<Filter>(filterLI => new Filter(filterLI));

const isAtLeastOneBrandSelected = (): boolean => Object.keys(activeBrandFilters).some(k => activeBrandFilters[k]);

const updateSearchParamsInUrl = (): void => {
  const params: URLSearchParams = new URLSearchParams();

  if (isAtLeastOneBrandSelected())
    params.append('brand', Object.keys(activeBrandFilters)
      .reduce((v, k) => {
        if (activeBrandFilters[k])
          return v.length ? `${v}#${k}` : k;
        else
          return v;
      }, ''));

  filters.forEach((f: Filter) => {
    const urlParam: ReturnType<Filter['toUrlParam']> = f.toUrlParam();

    if (urlParam)
      params.append(String(urlParam[0]), encodeURIComponent(urlParam[1]));
  });

  history.pushState(null, document.title, location.pathname + (params.toString() ? '?' + params.toString() : ''));
};

// #### FILTER ####
const filter = () => {
  products.forEach(product => {
    if (isAtLeastOneBrandSelected() && !activeBrandFilters[product.brand])
      return product.show(false);

    product.show(!filters.some(f => !f.testProduct(product)));
  });
};

// #### UTILIZE SEARCH PARAMS ####
{
  const urlSearchParams = new URLSearchParams(document.location.search.substring(1));
  const params: { [key: number]: string[] } = {};

  for (const param of urlSearchParams)
    params[Number(param[0])] = decodeURIComponent(param[1]).split('#').map(v => decodeURIComponent(v));

  filters.forEach(f => {
    if (params[f.specificationId])
      f.applyParam(params[f.specificationId]);
  });

  filter();
}
// #### SET Listeners ####
brandSpecification.forEach(input => input.addEventListener('click', () => {
  activeBrandFilters[Number(input.value)] = input.checked;
  filter();
  updateSearchParamsInUrl();
}));
