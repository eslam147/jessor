function mapName(e) {
    if (e.length > 0) {
        return e
            .map(({ name }) => {
                return name;
            })
            .join(",");
    } else {
        return e.name;
    }
}

function mapKeyVal(e) {
    if (e.length > 0) {
        return e.map((id) => {
            keyid = id.option_name;
            return { [keyid]: id.name };
        });
    } else {
        return [{ [e.option_name]: e.name }];
    }
}
let removeBtnNode = () => {
    let col = document.createElement("div");
    col.classList.add("col-1");
    col.innerHTML = `<button tabindex="-1" type="button" class="btn remove_row_variation btn-icon btn-rounded btn-sm btn-light-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-trash-2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>`;
    return col;
};
let optionsNode = (key = null) => {
    let col = document.createElement("div");
    col.classList.add("col-4");
    let input_options = document.createElement("input");
    input_options.setAttribute("type", "text");
    if (key != null) {
        input_options.setAttribute("value", key);
    }
    input_options.classList.add("form-control", "form-control-sm");

    col.appendChild(input_options);

    return col;
};
let variationsNode = () => {
    let col = document.createElement("div");
    col.classList.add("col-7");

    let input_variations = document.createElement("input");
    input_variations.classList.add(
        "form-control",
        "form-control-sm",
        "tag_variation"
    );
    col.appendChild(input_variations);
    let tag = new Tagify(input_variations, {
        pattern: /^[a-zA-Z0-9\u0600-\u06FF\s]+$/,
    });

    return { col: col, tag: tag };
};
let var_unique_id = 2;
function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min) + min);
}

let appendInputVariation = (values_box, variation_values) => {
    const cartesian = (...a) =>
        a.reduce((a, b) => a.flatMap((d) => b.map((e) => [d, e].flat())));

    let productValues = Object.values(variation_values);

    productValues = productValues.map(({ values }) => values);

    let permutations = cartesian(...productValues).map((e) => {
        return {
            combName: mapName(e),

            combKeyVal: mapKeyVal(e),
        };
    });

    values_box.innerHTML = "";
    for (let x in permutations) {
        let new_variantRow = document.createElement("div");
        new_variantRow.classList.add("row", "align-items-center");
        new_variantRow.innerHTML = `
                        <div class="col-3">
                            <label class="form-label"></label>
                            <input type="hidden"  class="form-control form-control-sm" name="variations[${x}][content]" value='${JSON.stringify(
            permutations[x].combKeyVal
        )}' readonly>
                            <input type="text" tabindex="-1" class="form-control border-0 form-control-sm text-black" value="${
                                permutations[x].combName
                            }" readonly>
                        </div>
                        <div class="col-8">
                            <div class="mb-2">
                                <label class="form-label">كميه</label>
                                <input type="number" name="variations[${x}][qty]" class="form-control form-control-sm">
                            </div>
                        </div>
                        <div class="col-1">
                        <button type="button" tabindex="-1" class="btn remove_variation_box btn-icon btn-rounded btn-sm btn-light-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="6" height="6"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="feather feather-trash-2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
                                    </button>
                        </div>
                        `;
        values_box.appendChild(new_variantRow);
    }
};
function removeAllData(obj) {
    for (let prop in obj) {
        if (obj.hasOwnProperty(prop)) {
            delete obj[prop];
        }
    }
}
function isJsonString(str) {
    try {
        return JSON.parse(str);
    } catch (e) {
        return null;
    }
    return null;
}
function appendNewVaritation(key = null, current_values = [], combainted = []) {
    combainted = isJsonString(combainted) ?? [];
    // console.log(combainted);
    let currentUniqueNumber = getRandomInt(168, 2564665);
    let row = document.createElement("div");
    row.uiqueId = currentUniqueNumber;
    row.classList.add("row", "align-items-center", "mb-2");
    let variation = variationsNode();
    let tagify = variation.tag;
    let options = optionsNode(key);
    let option_entry = options.querySelector("input");
    if (current_values.length > 0) {
        tagify.addTags(current_values);
    }

    combantioned_variations[currentUniqueNumber] = {
        name: key ?? "",
        values: combainted,
    };
    // console.log(combantioned_variations);
    option_entry.addEventListener("change", function (e) {
        // console.log(combantioned_variations);

        let values = combantioned_variations[currentUniqueNumber].values;
        for (let i = 0; i < values.length; i++) {
            values[i].option_name = this.value;
        }
    });
    // console.log(combantioned_variations);

    tagify.on("add", function (e) {
        let variant_name = e.detail.data.value,
            option_value = option_entry.value;

        combantioned_variations[currentUniqueNumber].values.push({
            name: variant_name,
            option_name: option_value,
        });

        appendInputVariation(
            $("#variations_combination")[0],
            combantioned_variations
        );
    });

    tagify.on("remove", function (e) {
        let variant_name = e.detail.data.value;

        for (let x in combantioned_variations[currentUniqueNumber].values) {
            let variation = combantioned_variations[currentUniqueNumber];
            let val = variation.values[x];
            if (val.name == variant_name) {
                variation.values.splice(x, 1);
            }
        }

        appendInputVariation(
            $("#variations_combination")[0],
            combantioned_variations
        );
    });

    row.append(options, variation.col, removeBtnNode());

    $(".variations_container")[0].appendChild(row);
}
