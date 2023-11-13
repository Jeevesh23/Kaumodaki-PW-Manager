document.addEventListener("DOMContentLoaded", function() {
    const dropdownBtns = document.querySelectorAll(".dropdown-btn");
    dropdownBtns.forEach((btn) => {
        btn.addEventListener("click", function() {
            const dropdownContent = this.nextElementSibling;
            if (dropdownContent.style.display === "block") {
                dropdownContent.style.display = "none";
            } else {
                dropdownContent.style.display = "block";
            }
        });
    });
});

function mypwstrength(elem) {
    var dataToSend = elem.closest('tr').firstElementChild.textContent.trim();
    var formData = new FormData();
    formData.append("data", dataToSend);
    var url = "/strength-analysis";

    redirectToPhp(url, formData);
}

function redirectToPhp(url, formData) {
    var form = document.createElement("form");
    form.setAttribute("method", "get");
    form.setAttribute("action", url);

    formData.forEach(function(value, key) {
        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", key);
        input.setAttribute("value", value);
        form.appendChild(input);
    });

    document.body.appendChild(form);

    form.submit();
}

function mydelete(elem) {
    var delContent = elem.closest('tr').firstElementChild.textContent.trim();
    var result = confirm('Do you want to delete account ' + delContent + ' ?');
    if (result) {
        var xhr = new XMLHttpRequest();
        var url = "/vault/delete";

        xhr.open("POST", url, true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var response = xhr.responseText;
                alert(response);
                location.reload();
            }
        };
        xhr.send("data=" + delContent);
    }
}

document.getElementById("dashboard-body").addEventListener("click", function(event) {
    var clickedElement = event.target;
    if (clickedElement.parentElement.classList.contains("view-button") || clickedElement.parentElement.parentElement.classList.contains("view-button")) {
        var clickedparElement = event.target.closest('td').parentElement;
        var reqelem = document.getElementById('dashboard-body');
        if (clickedparElement === reqelem.children[reqelem.children.length - 2]) {
            var table = document.querySelector("main .passwords table");
            var rows = table.querySelectorAll("tr");
            var secondToLastRow = rows[rows.length - 2];
            var tds = secondToLastRow.querySelectorAll("td");
            if (tds[0].style.borderBottomLeftRadius != "0px") {
                tds[0].style.borderBottomLeftRadius = "0px";
                tds[tds.length - 1].style.borderBottomRightRadius = "0px";
            } else {
                tds[0].style.borderBottomLeftRadius = "2rem";
                tds[tds.length - 1].style.borderBottomRightRadius = "2rem";
            }
        }
    }
});
const card = document.querySelector('.user-profile');

card.addEventListener('click', () => {
    const cardInner = document.querySelector('.flip-card-inner');
    cardInner.classList.toggle('flipped');
    card.style.transform = card.style.transform === 'rotateY(180deg)' ? 'rotateY(0deg)' : 'rotateY(180deg)';
});

$(document).ready(function() {
    $('.edit-button').click(function() {
        var element = document.getElementById("myElement");
        document.body.classList.toggle('blur');
        if (element.style.display === "none" || element.style.display === "") {
            element.style.display = "block";
        } else {
            element.style.display = "none";
        }
        var editContent = this.closest('tr').firstElementChild.textContent.trim();
        // Create a data object to send to the server
        var dataToSend = {
            edit: editContent
        };

        $.ajax({
            url: '/vault/edit',
            method: 'POST',
            data: dataToSend,
            success: function(data) {
                if (data.error) {
                    console.error('Error: ' + data.error);
                } else {
                    var responseData = JSON.parse(data);
                    $('#websiteField').val(responseData.Website);
                    $('#linkField').val(responseData.Link);
                    $('#usernameField').val(responseData.Username);
                    $('#passwordField').val(responseData.DecPwd);
                    $('input[name="Type"][value="' + responseData['Wrd/Phr'] + '"]').prop('checked', true);
                    if (responseData.RST === '1') {
                        $('input[name="Reset"]').prop('checked', true);
                    } else {
                        $('input[name="Reset"]').prop('checked', false);
                    }
                    $('#descriptionField').val(responseData.Description);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error: ' + error);
            }
        });
    });
});

function closeEdit() {
    var element = document.getElementById("myElement");
    document.body.classList.remove('blur');
    element.style.display = "none";
}

$(document).ready(function() {
    $('.view-button').click(function() {
        var dropdownContent = this.closest('td').parentElement.nextElementSibling;
        if (dropdownContent.style.display === 'table-row') {
            dropdownContent.style.display = 'none';
        } else {
            dropdownContent.style.display = 'table-row';
        }
        var viewContent = this.closest('tr').firstElementChild.textContent.trim();
        // Create a data object to send to the server
        var dataToSend = {
            view: viewContent
        };

        $.ajax({
            url: '/vault/view-password',
            method: 'POST',
            data: dataToSend,
            success: function(data) {
                if (data.error) {
                    console.error('Error: ' + data.error);
                } else {
                    var responseData = JSON.parse(data);
                    var website = responseData.Website;
                    $('#namefield_' + website).val(responseData.Username);
                    $('#hiddenpwd_' + website).val(responseData.DecPwd);

                    var displayedPassword = responseData.DecPwd.length > 10 ?
                        responseData.DecPwd.substring(0, 10) + '...' :
                        responseData.DecPwd;
                    $('#passwordfield_' + website).val(displayedPassword);

                    var reset = responseData.RST;
                    if (reset == 1) {
                        var dateOnly = responseData.Add_Date.split(' ')[0];
                        var originalDate = new Date(dateOnly);
                        var newDate = new Date(originalDate);
                        newDate.setDate(originalDate.getDate() + 180);
                        var formattedNewDate = newDate.toISOString().split('T')[0];
                        $('#datefield_' + website).val(formattedNewDate);
                    } else {
                        $('#datefield_' + website).val('-');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error: ' + error);
            }
        });
    });
});

function viewPwd(button) {
    var row = button.closest('tr');
    var website = row.getAttribute('data-website');

    var passwordField = $('#passwordfield_' + website);
    var hiddenPwdField = $('#hiddenpwd_' + website);
    var visibilityButton = $(button);

    var isVisible = passwordField.attr('type') === 'text';

    if (isVisible) {
        passwordField.attr('type', 'password');
        visibilityButton.html('visibility_off');
        visibilityButton.css('color', '#d9534f');
    } else {
        passwordField.attr('type', 'text');
        visibilityButton.html('visibility');
        visibilityButton.css('color', '#5cb85c');
    }

}

function copyPwd(button) {
    var row = button.closest('tr');
    var website = row.getAttribute('data-website');
    var hiddenPwd = $('#hiddenpwd_' + website).val();

    var tempInput = document.createElement('input');
    tempInput.value = hiddenPwd;
    document.body.appendChild(tempInput);

    tempInput.select();
    tempInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    alert('Password copied to clipboard!');
}

document.getElementById("insertbutton").addEventListener("click", function() {
    event.preventDefault();

    const selectedRadio = document.querySelector('input[name="Type"]:checked');

    if (selectedRadio) {
        const sizeSlider = selectedRadio.value === "0" ? document.getElementById("passwordSizeSlider") : document.getElementById("passphraseSizeSlider");
        const size = sizeSlider.value;

        const endpoint = selectedRadio.value === "0" ? '/vault/password' : '/vault/passphrase';

        fetch(`${endpoint}?size=${size}`)
            .then(response => response.text())
            .then(passwordString => {
                document.getElementById("passwordField").value = passwordString;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
});
const passwordRadio = document.getElementById("passwordRadio");
const passphraseRadio = document.getElementById("passphraseRadio");
const passwordSizeSlider = document.getElementById("passwordSizeSlider");
const passphraseSizeSlider = document.getElementById("passphraseSizeSlider");
const passwordSizeValue = document.getElementById("passwordSizeValue");
const passphraseSizeValue = document.getElementById("passphraseSizeValue");

function updateSliderVisibility() {
    if (passwordRadio.checked) {
        passwordSizeSlider.style.display = "block";
        passphraseSizeSlider.style.display = "none";
    } else if (passphraseRadio.checked) {
        passwordSizeSlider.style.display = "none";
        passphraseSizeSlider.style.display = "block";
    }
}

passwordRadio.addEventListener("change", updateSliderVisibility);
passphraseRadio.addEventListener("change", updateSliderVisibility);

passwordSizeSlider.addEventListener("input", function() {
    passwordSizeValue.textContent = passwordSizeSlider.value;
});

passphraseSizeSlider.addEventListener("input", function() {
    passphraseSizeValue.textContent = passphraseSizeSlider.value;
});

updateSliderVisibility();
const resetButton = document.getElementById("button_R");

document.getElementById("button_R").addEventListener("click", function(event) {
    event.preventDefault();
    var inputFieldsToReset = document.querySelectorAll('.add_password .input-box input:not([name="Website"])');
    inputFieldsToReset.forEach(function(input) {
        if (input.type === "radio" || input.type === "checkbox") {
            input.checked = false;
        } else {
            input.value = '';
        }
        passwordSizeSlider.value = 16;
        passphraseSizeSlider.value = 5;
        passwordSizeValue.textContent = 16;
        passphraseSizeValue.textContent = 5;
        document.getElementById("descriptionField").value = '';
    });
});