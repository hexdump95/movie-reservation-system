<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;

class ReportRepository
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRevenueGroupByMonth(): ?array
    {
        $query = "
            select
                distinct date,
                sum(occupied_seats) as occupied_seats,
                sum(canceled_seats) as canceled_seats,
                sum(total_seats) as total_seats,
                case when sum(revenue) is null then 0 else sum(revenue) end as revenue,
                (sum(occupied_seats) / (case when sum(total_seats) = 0 then 1 else sum(total_seats) end) * 100)::numeric(3, 2) assist_percent
            from (
            select	sh.id,
                date_trunc('month', sh.date_start)::date date,
                count(distinct case when bs.name = 'PAID' then tise.id end) occupied_seats,
                count(distinct case when bs.name != 'PAID' then tise.id end) canceled_seats,
                count(distinct thse.id) total_seats,
                
                (select sum(
                case
                    when bs.name = 'PAID' 
                    then ti.price
                    else ti.price / 2
                    end
                )
                from ticket ti
                join book b2 on ti.book_id = b2.id
                left join status_book sb2 on sb2.book_id = b2.id and sb2.date_to is null
                left join book_status bs on bs.id = sb2.book_status_id
                where b2.showtime_id = sh.id) revenue
            from movie m
            left join showtime sh on sh.movie_id = m.id
            left join theater th on th.id = sh.theater_id
            left join seat thse on thse.theater_id = th.id and thse.code <> ''
            left join book b on b.showtime_id = sh.id
            left join status_book sb on sb.book_id = b.id and sb.date_to is null
            left join book_status bs on bs.id = sb.book_status_id
            left join ticket ti on ti.book_id = b.id
            left join seat tise on tise.id = ti.seat_id
            where
                sh.id is not null
            group by sh.id, m.id
            ) sq
            group by sq.date
            order by sq.date
            ;
        ";

        $statement = $this->entityManager->getConnection()->prepare($query);
        $showtimes = $statement->executeQuery()->fetchAllAssociative();
        return $showtimes == [] ? null : $showtimes;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getRevenueByMonthGroupedByShowtime(string $date): ?array
    {
        $query = "
                select 
                distinct sh.id as showtime_id,
                count(distinct thse.id) total_seats,
                count(distinct case when bs.name = 'PAID' then tise.id end) occupied_seats,
                count(distinct case when bs.name != 'PAID' then tise.id end) canceled_seats,
                count(distinct thse.id) - count(distinct case when bs.name = 'PAID' then tise.id end) available_seats,
                (select sum(
                    case
                        when bs.name = 'PAID' 
                        then ti.price
                        else ti.price / 2
                    end
                )
                from ticket ti
                join book b2 on ti.book_id = b2.id
                left join status_book sb2 on sb2.book_id = b2.id and sb2.date_to is null
                left join book_status bs on bs.id = sb2.book_status_id
                where b2.showtime_id = sh.id) revenue,
                th.number theater_number,
                sh.date_start,
                m.title movie_title
            from showtime sh
            left join book b on sh.id = b.showtime_id
            left join theater th on th.id = sh.theater_id
            left join movie m on m.id = sh.movie_id
            left join status_book sb on sb.book_id = b.id and sb.date_to is null
            left join book_status bs on bs.id = sb.book_status_id
            left join ticket ti on ti.book_id = b.id
            left join seat tise on tise.id = ti.seat_id
            left join seat thse on thse.theater_id = th.id and thse.code <> ''
            where date_trunc('month', sh.date_start::date) = date_trunc('month', :date::date)
            group by sh.id, th.number, sh.date_start, m.title
            ;
        ";

        $statement = $this->entityManager->getConnection()->prepare($query);
        $statement->bindValue('date', $date);
        $showtimes = $statement->executeQuery()->fetchAllAssociative();
        return $showtimes == [] ? null : $showtimes;
    }

}
