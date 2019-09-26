# org.etui.contacts

Custom code concerning contacts.

## Token: etui_addressee

This custom token enhances the standard addressee token.

If the current contact is an organization, it returns the name of the organization.

If the current contact is an individual:

 * if his/her address is of type "home" or of type "magazine without organization": it returns the addressee of the current contact (in most cases: prefix + first name + last name)
 * else, if his/her address refers to another contact: it returns the name of that contact and on the next line the addressee of the current contact
 * else, it returns the name of the current employer and on the next line the addressee of the current contact
 
