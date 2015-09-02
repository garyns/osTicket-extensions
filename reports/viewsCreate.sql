-- NOTE: The SQL below assumes your tables are prefixed with ost_

CREATE VIEW tickets_opened_by_month AS SELECT DATE_FORMAT(`created`,'%Y-%m') as d, count(*) as opened, 0 as closed from ost_ticket GROUP BY YEAR(created), MONTH(created);
CREATE VIEW tickets_closed_by_month AS SELECT DATE_FORMAT(`closed`,'%Y-%m') as d, 0 as opened, count(*) as closed from ost_ticket WHERE status='closed' GROUP BY YEAR(closed), MONTH(closed);

CREATE VIEW tickets_by_month_union AS select * from tickets_opened_by_month UNION ALL select * from tickets_closed_by_month;
CREATE VIEW tickets_by_month AS SELECT d, sum(opened) as opened, sum(closed) as closed, sum(opened) - sum(closed) as diff from tickets_by_month_union group by d ORDER BY d;

CREATE VIEW tickets_last_entry AS SELECT tt.ticket_id, t.created, tt.created as threadUpdated, t.closed, t.status, body, subject FROM ost_ticket_thread tt, ost_ticket__cdata td, ost_ticket t WHERE t.ticket_id = tt.ticket_id AND tt.ticket_id = td.ticket_id AND tt.id in (SELECT max(id) FROM ost_ticket_thread tt, ost_ticket t WHERE t.ticket_id = tt.ticket_id GROUP BY tt.ticket_id) ORDER BY tt.ticket_id;

