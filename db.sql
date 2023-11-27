ALTER TABLE ticket_user ADD UNIQUE (user_id, ticket_id);


# CREATE TABLE your_table_name (
#                                  user_id INT,
#                                  ticket_id INT,
#     /* другие столбцы, если они есть */
#                                  UNIQUE KEY unique_user_ticket (user_id, ticket_id)
# );