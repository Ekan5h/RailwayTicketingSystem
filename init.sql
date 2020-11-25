create table trains( train_id serial primary key, name varchar(256) not null, created_on timestamp);
create table train_sched( train_id integer, date date, num_AC integer not null, num_SL integer not null, released_on timestamp, primary key(train_id, date), foreign key(train_id) references trains(train_id));
create table coach(coach_type char(2), berth integer, berth_type char(2), primary key(coach_type, berth));
CREATE TABLE booking_agents(name varchar(256), email varchar(256) primary key, password varchar(256));
CREATE TABLE all_tickets_log(booking_agent varchar(256), pnr bigint primary key, created_on timestamp);

-- Before insert of train trigger function
CREATE OR REPLACE FUNCTION _pre_insert_schedule()
RETURNS TRIGGER
LANGUAGE PLPGSQL
AS $$
DECLARE
gap int;
BEGIN
gap = new.date::date - CURRENT_DATE::date;
if gap < 7 then
raise exception 'Invalid date!';
end if;
if new.num_ac + new.num_sl = 0 then
raise exception 'Zero coaches!';
end if;
return new;
END;
$$;

CREATE TRIGGER pre_insert_schedule
BEFORE INSERT
ON train_sched
FOR EACH ROW
EXECUTE PROCEDURE _pre_insert_schedule();



-- After insert of train trigger function
CREATE OR REPLACE FUNCTION _on_insert_schedule()
RETURNS TRIGGER
LANGUAGE PLPGSQL
AS $$
DECLARE
seat record;
BEGIN
EXECUTE format('CREATE TABLE %I (pnr bigint primary key, booking_agent varchar(256));', 'bookings_' || NEW.train_id::text || '_' || to_char(NEW.date, 'yyyy_mm_dd'));
EXECUTE format('CREATE TABLE %I (coach char(5), berth integer, primary key(coach, berth));', 'empty_seats_' || NEW.train_id::text || '_' || to_char(NEW.date, 'yyyy_mm_dd'));
for seat in execute format('select * from getAllBerths(%L, %L, %L);', NEW.train_id, NEW.date, 'AC') loop
execute format('INSERT INTO %I VALUES(%L, %L)',  'empty_seats_' || NEW.train_id::text || '_' || to_char(NEW.date, 'yyyy_mm_dd'), seat.coach, seat.berth);
end loop;
for seat in execute format('select * from getAllBerths(%L, %L, %L);', NEW.train_id, NEW.date, 'SL') loop
execute format('INSERT INTO %I VALUES(%L, %L)',  'empty_seats_' || NEW.train_id::text || '_' || to_char(NEW.date, 'yyyy_mm_dd'), seat.coach, seat.berth);
end loop;
RETURN NEW;
END;
$$;

CREATE TRIGGER on_insert_schedule
AFTER INSERT
ON train_sched
FOR EACH ROW
EXECUTE PROCEDURE _on_insert_schedule();

--Get all berths in the train
CREATE OR REPLACE FUNCTION getAllBerths(trainID integer, day date, coachType char(2))
RETURNS table(coach char(5), berth integer)
LANGUAGE plpgsql
AS $$
DECLARE
num integer;
i integer;
j integer;
BEGIN
if coachType = 'AC' then
SELECT INTO num
num_AC FROM train_sched
WHERE train_id = trainID AND date = day;
else
SELECT INTO num
num_SL FROM train_sched
WHERE train_id = trainID AND date = day;
end if;
for i in 1..num loop
for j in SELECT c.berth FROM coach c WHERE coach_type = coachType loop
coach := coachType || trim(to_char(i,'000'));
berth := j;
return next;
end loop;
end loop;
END;
$$;

-- get booked berths  (Unused in business logic)
create or replace function get_booked_berths (
train_id integer,
date date
)
returns table (
coach char(5),
berth int
) LANGUAGE plpgsql AS $$
declare
booking record;
ticket record;
pnr bigint;
begin
for booking in execute format('select * from %I;', 'bookings_' || train_id::text || '_' || to_char(date, 'yyyy_mm_dd')) loop
pnr := booking.pnr;
for ticket in execute format('select coach, berth from %I;', 'ticket_' || pnr::text) loop
coach := ticket.coach;
berth := ticket.berth;
return next;
end loop;
end loop;
end; $$;

-- Unbooked berths
create or replace function get_free_berths (
coach_type char(2),
num_seats int,
train_id int,
date date
)
returns table (
coach char(5),
berth int
)
LANGUAGE plpgsql AS $$
declare
available int;
begin
EXECUTE format('select count(*) from %I where coach like %L', 'empty_seats_'||train_id||to_char(date,'_yyyy_mm_dd'), coach_type ||'%') into available;
if available < num_seats then
raise exception '% seats not available', num_seats;
else
return query EXECUTE format('select * from %I where coach like %L limit %L', 'empty_seats_'||train_id||to_char(date,'_yyyy_mm_dd'), coach_type ||'%', num_seats);
end if;
end; $$;

-- Get PNR
CREATE OR REPLACE FUNCTION getPNR(
trainID integer,
day date,
coach char(5),
berth integer
)
RETURNS bigint
LANGUAGE plpgsql
AS $$
DECLARE
coachType integer;
pnr bigint;
BEGIN
if substr(coach, 1,2) = 'AC' then
coachType := 1;
else
coachType := 0;
end if;
pnr := (trim(to_char(trainID, '00000')) || trim(to_char(day, 'yymmdd')) || coachType || substr(coach, 3) || trim(to_char(berth, '000')))::bigint;
return power(pnr , 817::bigint);
END;
$$;

-- O(log) power
CREATE OR REPLACE FUNCTION power(x bigint, y bigint)
RETURNS bigint
LANGUAGE plpgsql
AS $$
DECLARE
mod bigint := 1000000214000001449;
t bigint;
BEGIN
if y=0 then
return 1;
else
t = power(x, y/2);
t := ((t::decimal*t::decimal)%mod)::bigint;
if y%2 = 1 then
t := ((t::decimal*x::decimal)%mod)::bigint;
end if;
return t;
end if;
END;
$$;

-- Decrypt PNR
CREATE OR REPLACE FUNCTION decrypt(x bigint)
RETURNS bigint
LANGUAGE plpgsql
AS $$
BEGIN
return power(x, 1223990467564261::bigint);
END;
$$;

-- Get ticket from pnr
CREATE OR REPLACE FUNCTION getTicket(pnr bigint)
RETURNS table(berth integer,coach_type char(2),name varchar(256), age integer, gender char(1), coach char(5), berth_type char(2))
LANGUAGE plpgsql
AS $$
BEGIN
return query EXECUTE format('SELECT * FROM (SELECT *, substr(coach, 1, 2)::char(2) coach_type FROM %I) temp NATURAL JOIN coach;', 'ticket_' || pnr::text);
END;
$$;

--Allot K berths and create a PNR
CREATE OR REPLACE FUNCTION allotK(
trainID integer,
day date,
k integer,
ct char(2),
booking_agent varchar(256),
names varchar(256)[],
ages integer[],
genders char(1)[]
)
RETURNS bigint
LANGUAGE plpgsql
AS $$
DECLARE
f boolean := TRUE;
seat record;
pnr bigint;
i integer := 1;
temp text;
BEGIN
for seat in execute format('select * from get_free_berths(%L, %L, %L, %L);', ct, k, trainID, day) loop
if f then
f := FALSE;
pnr:= getPNR(trainID, day, seat.coach, seat.berth);
execute format('INSERT INTO %I VALUES(%L, %L)',  'bookings_' || trainID::text || '_' || to_char(day, 'yyyy_mm_dd'), pnr, booking_agent);
execute format('CREATE TABLE %I (name varchar(256), age integer, gender char(1), coach char(5), berth integer);', 'ticket_' || pnr::text);
end if;
execute format('DELETE FROM %I WHERE coach=%L AND berth=%L', 'empty_seats_'||trainID||to_char(day,'_yyyy_mm_dd'), seat.coach, seat.berth);
execute format('INSERT INTO %I VALUES(%L, %L, %L, %L, %L);', 'ticket_' || pnr::text, names[i], ages[i], genders[i], seat.coach, seat.berth);
i := i+1;
end loop;
temp := replace(replace(booking_agent, '@','_'),'.','_');
execute format('INSERT INTO %I VALUES(%L, %L);', 'past_bookings_'||temp, pnr, now());
execute format('INSERT INTO all_tickets_log VALUES(%L, %L, %L);', booking_agent, pnr, now());
return pnr;
END;
$$;

--Inserts into coach
INSERT INTO coach VALUES('AC', 1, 'LB');
INSERT INTO coach VALUES('AC', 2, 'LB');
INSERT INTO coach VALUES('AC', 3, 'UB');
INSERT INTO coach VALUES('AC', 4, 'UB');
INSERT INTO coach VALUES('AC', 5, 'SL');
INSERT INTO coach VALUES('AC', 6, 'SU');
INSERT INTO coach VALUES('AC', 7, 'LB');
INSERT INTO coach VALUES('AC', 8, 'LB');
INSERT INTO coach VALUES('AC', 9, 'UB');
INSERT INTO coach VALUES('AC', 10, 'UB');
INSERT INTO coach VALUES('AC', 11, 'SL');
INSERT INTO coach VALUES('AC', 12, 'SU');
INSERT INTO coach VALUES('AC', 13, 'LB');
INSERT INTO coach VALUES('AC', 14, 'LB');
INSERT INTO coach VALUES('AC', 15, 'UB');
INSERT INTO coach VALUES('AC', 16, 'UB');
INSERT INTO coach VALUES('AC', 17, 'SL');
INSERT INTO coach VALUES('AC', 18, 'SU');
INSERT INTO coach VALUES('SL', 1, 'LB');
INSERT INTO coach VALUES('SL', 2, 'MB');
INSERT INTO coach VALUES('SL', 3, 'UB');
INSERT INTO coach VALUES('SL', 4, 'LB');
INSERT INTO coach VALUES('SL', 5, 'MB');
INSERT INTO coach VALUES('SL', 6, 'UB');
INSERT INTO coach VALUES('SL', 7, 'SL');
INSERT INTO coach VALUES('SL', 8, 'SU');
INSERT INTO coach VALUES('SL', 9, 'LB');
INSERT INTO coach VALUES('SL', 10, 'MB');
INSERT INTO coach VALUES('SL', 11, 'UB');
INSERT INTO coach VALUES('SL', 12, 'LB');
INSERT INTO coach VALUES('SL', 13, 'MB');
INSERT INTO coach VALUES('SL', 14, 'UB');
INSERT INTO coach VALUES('SL', 15, 'SL');
INSERT INTO coach VALUES('SL', 16, 'SU');
INSERT INTO coach VALUES('SL', 17, 'LB');
INSERT INTO coach VALUES('SL', 18, 'MB');
INSERT INTO coach VALUES('SL', 19, 'UB');
INSERT INTO coach VALUES('SL', 20, 'LB');
INSERT INTO coach VALUES('SL', 21, 'MB');
INSERT INTO coach VALUES('SL', 22, 'UB');
INSERT INTO coach VALUES('SL', 23, 'SL');
INSERT INTO coach VALUES('SL', 24, 'SU');

CREATE OR REPLACE FUNCTION total_seats(trainID integer, day date)
RETURNS integer
LANGUAGE plpgsql
AS $$
DECLARE
numAC integer;
numSL integer;
coachAC integer;
coachSL integer;
BEGIN
select into numAC, numSL num_AC, num_SL from train_sched where train_id = trainID and date = day;
select count(*) into coachAC from coach where coach_type='AC';
select count(*) into coachSL from coach where coach_type='SL';
return coachAC*numAC + coachSL*numSL;
END;
$$;

CREATE OR REPLACE FUNCTION empty_seats(train_id integer, day date)
RETURNS integer
LANGUAGE plpgsql
AS $$
DECLARE
emp integer;
BEGIN
EXECUTE format('SELECT count(*) FROM %I', 'empty_seats_'||train_id||to_char(day,'_yyyy_mm_dd')) into emp;
return emp;
END;
$$;

CREATE OR REPLACE FUNCTION getTrainDetails(pnr bigint)
RETURNS table(train_id integer, date date)
LANGUAGE plpgsql
AS $$
DECLARE
pn varchar(20);
BEGIN
pn := to_char(decrypt(pnr), '000000000000000000');
train_id := substr(pn, 1, 6);
date := ('20'||substr(pn, 7, 2)||'-'||substr(pn, 9, 2)||'-'||substr(pn,11,2))::date;
return next;
END;
$$;



CREATE OR REPLACE FUNCTION _on_insert_agent()
RETURNS TRIGGER
LANGUAGE PLPGSQL
AS $$
DECLARE
temp text;
BEGIN
temp := replace(replace(NEW.email, '@','_'),'.','_');
EXECUTE format('CREATE TABLE %I (pnr bigint primary key, created_on timestamp);', 'past_bookings_' || temp);
RETURN NEW;
END;
$$;

CREATE TRIGGER on_insert_agent
AFTER INSERT
ON booking_agents
FOR EACH ROW
EXECUTE PROCEDURE _on_insert_agent();

CREATE OR REPLACE FUNCTION getTrainID(pnr bigint)
RETURNS integer
LANGUAGE plpgsql
AS $$
DECLARE
pn varchar(20);
train_id integer;
BEGIN
pn := to_char(decrypt(pnr), '000000000000000000');
train_id := substr(pn, 1, 6)::integer;
return train_id;
END;
$$;

CREATE OR REPLACE FUNCTION getTrainDate(pnr bigint)
RETURNS date
LANGUAGE plpgsql
AS $$
DECLARE
pn varchar(20);
BEGIN
pn := to_char(decrypt(pnr), '000000000000000000');
return ('20'||substr(pn, 7, 2)||'-'||substr(pn, 9, 2)||'-'||substr(pn,11,2))::date;
END;
$$;