define([
    'jquery',
    'core/ajax',
    'core/templates',
    'core/str',
    'qrcode',
], function($, Ajax, Templates, str, QRCode) {
    return {
        init: function(courseID, teachers, canManage, user, groupLink) {
            this.courseID = courseID;
            this.teachers = teachers;
            this.canManage = canManage;
            this.user = user;
            this.groupLink = groupLink;

            // Add the user to the contacts list if they have enabled WhatsApp.
            this.contacts = this.teachers;
            if (this.user.whatsapp_enable) {
                this.contacts = this.teachers.concat(this.user);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', this.moveMenuItem.bind(this));
            } else {
                this.moveMenuItem();
            }
        },
        moveMenuItem: function() {
            let whatsappItemLink = document.querySelector('li[data-key="whatsapp"] a');
            if (! whatsappItemLink) {
                return;
            }

            // Clone the share link.
            let whatsappItemClone = whatsappItemLink.cloneNode(true);
            // Remove role and tabindex attributes
            whatsappItemClone.removeAttribute('role');
            whatsappItemClone.removeAttribute('tabindex');
            // Modify the cloned link to be an info button with the fa-share icon.
            whatsappItemClone.classList.remove('dropdown-item');
            whatsappItemClone.classList.add('btn', 'btn-sm', 'btn-info', 'd-flex', 'align-items-center', 'ml-3');
            whatsappItemClone.style.height = 'fit-content';
            whatsappItemClone.style.color = 'white';
            whatsappItemClone.innerHTML = '<i class="fa fa-whatsapp mr-2"></i> WhatsApp';

            // Change background color if there are no links configured
            let background = '#28a745';
            if (this.contacts.length === 0) {
                background = '#8f959e';
            }
            whatsappItemClone.style.backgroundColor = background;
            whatsappItemClone.style.borderColor = background;

            // Find the course title element and insert the cloned link next to it.
            let courseTitle = document.querySelector('.page-header-headings');
            if (courseTitle) {
                courseTitle.parentNode.insertBefore(whatsappItemClone, courseTitle.nextSibling);
            }

            // Prevent default action and show modal.
            whatsappItemClone.addEventListener('click', (event) => {
                event.preventDefault();
                this.showModal();
            });
            // Prevent default action and show modal.
            whatsappItemLink.addEventListener('click', (event) => {
                event.preventDefault();
                this.showModal();
            });
        },
        showModal: async function() {
            // Check if the modal already exists
            let existingModal = document.getElementById('whatsappModal');
            if (existingModal) {
                $('#whatsappModal').modal('show');
                return;
            }

            const context = {
                courseID: this.courseID,
                contacts: this.contacts,
                canManage: this.canManage,
                user: this.user,
                groupLink: this.groupLink
            };

            Templates.render('local_whatsapp/modal', context).done(function(html, js) {
                let that = this;

                // Append the modal to the body
                document.body.insertAdjacentHTML('beforeend', html);
                Templates.runTemplateJS(js);
                $('#whatsappModal').modal('show');

                // Generate QR codes and add copy event listeners
                this.contacts.forEach(function(contact) {
                    that.generateQRCode(contact);
                    that.addCopyEventListener(contact);
                });
            }.bind(this)).fail(function(ex) {
                console.error('Failed to render template', ex);
            });
        },
        generateQRCode: function(contact) {
            // Generate QR code
            let qrcodeContainer = document.getElementById('qrcode-' + contact.id);
            new QRCode(qrcodeContainer, {
                text: contact.whatsapp_link,
                width: 256,
                height: 256,
            });
        },
        addCopyEventListener: function(contact) {
            button = document.getElementById('copy-contact-' + contact.id + '-button');

            if (!button) {
                return;
            }

            console.log(contact.whatsapp_link);

            button.addEventListener('click', function() {
                navigator.clipboard.writeText(contact.whatsapp_link).then(async function() {
                    // Change tooltip text when copied.
                    let copiedlabel = await str.get_string('copied_clipboard', 'local_whatsapp');
                    button.setAttribute('data-original-title', copiedlabel);
                    $(button).tooltip('show');
                }).catch(function(error) {
                    console.error('Could not copy text: ', error);
                });

                // Reset tooltip title when the mouse leaves the button.
                button.addEventListener('mouseleave', async function() {
                    let copylabel = await str.get_string('copy_clipboard', 'local_whatsapp');
                    button.setAttribute('data-original-title', copylabel);
                    $(button).tooltip('hide');
                }, {once: true});
            });
        }
    };
});
