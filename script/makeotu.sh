#Environment settings
RED='\033[0;31m'
NC='\033[0m' # No Color
GREEN='\e[32m'

#read fname
otufolder=$1
primerseq=$2
matchop=$3
taxalg=$4
rdpdb=$5
conflevel=$6
trunlen=$7

#shift 7
shift 
shift 
shift 
shift

shift 
shift 
shift 

# Make folder for merged OTU table
# mkdir $otufolder
touch "${otufolder}/merged.fa"
cd ../script

for fname in "$@"; do
	if [[ $matchop == *"fwdrev"* ]]; then
		if [[ $matchop == *"full"* ]]; then

			# find backward primer and forward primer
			./usearch -search_oligodb "${fname}.fastq" -db "${primerseq}/primer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_primer.fastq"
			./usearch -search_oligodb "${fname}.fastq" -db "${primerseq}/bprimer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_bprimer.fastq"

			# Make reverse complement of backward primer seq
			./usearch -fastx_revcomp "${fname}_match_bprimer.fastq" -label_suffix _RC -fastqout "${fname}_match_bprimer_rc.fastq"

			#Second filtering
			./usearch -search_oligodb "${fname}_match_primer.fastq" -db "${primerseq}/bprimer_rc.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_fbprimer.fastq"
			./usearch -search_oligodb "${fname}_match_bprimer_rc.fastq" -db "${primerseq}/primer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_fbprimer2.fastq"

			# Merge two files
			cat "${fname}_match_fbprimer.fastq" "${fname}_match_fbprimer2.fastq" > "${fname}_concat.fastq"

		else

			# find backward primer and forward primer
			./usearch -search_oligodb "${fname}.fastq" -db "${primerseq}/primer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_primer.fastq"
			./usearch -search_oligodb "${fname}.fastq" -db "${primerseq}/bprimer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_bprimer.fastq"

			# Make reverse complement of backward primer seq
			./usearch -fastx_revcomp "${fname}_match_bprimer.fastq" -label_suffix _RC -fastqout "${fname}_match_bprimer_rc.fastq"

			# Merge two files
			cat "${fname}_match_primer.fastq" "${fname}_match_bprimer_rc.fastq" > "${fname}_concat.fastq"

		fi
	elif [[ $matchop == *"fwd"* ]]; then
		if [[ $matchop == *"full"* ]]; then

			# match with primer, bprimer_rc 
			./usearch -search_oligodb "${fname}.fastq" -db "${primerseq}/primer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_primer.fastq"	
			./usearch -search_oligodb "${fname}_match_primer.fastq" -db "${primerseq}/bprimer_rc.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_fbprimer_rc.fastq"

			# change filename
			mv "${fname}_match_fbprimer_rc.fastq" "${fname}_concat.fastq"
		else

			# match with primer, bprimer_rc 
			./usearch -search_oligodb "${fname}.fastq" -db "${primerseq}/primer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_primer.fastq"	

			# change filename
			mv "${fname}_match_primer.fastq" "${fname}_concat.fastq"

		fi
	elif [[ $matchop == *"rev"* ]]; then
		if [[ $matchop == *"full"* ]]; then

			# match with bprimer, primer_rc 
			./usearch -search_oligodb "${fname}.fastq" -db "${primerseq}/bprimer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_bprimer.fastq"	
			./usearch -search_oligodb "${fname}_match_primer.fastq" -db "${primerseq}/primer_rc.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_fbprimer_rc.fastq"

			# Make reverse complement of backward primer seq
			./usearch -fastx_revcomp "${fname}_match_fbprimer_rc.fastq" -label_suffix _RC -fastqout "${fname}_match_fbprimer_rc_rc.fastq"

			# change filename
			mv "${fname}_match_fbprimer_rc_rc.fastq" "${fname}_concat.fastq"
		else

			# match with primer, bprimer_rc 
			./usearch -search_oligodb "${fname}.fastq" -db "${primerseq}/bprimer.fa" -strand plus \
			  -maxdiffs 3 -matchedfq "${fname}_match_bprimer.fastq"	

			# Make reverse complement of backward primer seq
			./usearch -fastx_revcomp "${fname}_match_bprimer.fastq" -label_suffix _RC -fastqout "${fname}_match_bprimer_rc.fastq"

			# change filename
			mv "${fname}_match_bprimer_rc.fastq" "${fname}_concat.fastq"

		fi
	else
		# Error
		echo "ERROR: match option is not specified"
	fi

	# Filter fastq file and truncate
	if [[ $trunlen == *"nt"* ]]; then
		./usearch -fastq_filter "${fname}_concat.fastq" -fastq_maxee 0.5 -fastaout "${fname}_truncate.fa"
	elif [[ $trunlen == *"200"* ]]; then
		./usearch -fastq_filter "${fname}_concat.fastq" -fastq_trunclen 200 -fastq_maxee 0.5 -fastaout "${fname}_truncate.fa"
	elif [[ $trunlen == *"250"* ]]; then
		./usearch -fastq_filter "${fname}_concat.fastq" -fastq_trunclen 250 -fastq_maxee 0.5 -fastaout "${fname}_truncate.fa"
	else
		# Error
		echo "ERROR: truncate option is not specified"
	fi

	# Mark label
	newlabel=$(echo $fname | grep -P '(?<=IonXpress_)\d{3}' -o)
	sed -ie "s/>/>${newlabel}./g" "${fname}_truncate.fa"

	cat "${fname}_truncate.fa" >> "${otufolder}/merged.fa"

done

# dereplication
./usearch -derep_fulllength "${otufolder}/merged.fa" -fastaout "${otufolder}/derep.fa" -sizeout


# Clustering
./usearch -cluster_otus "${otufolder}/derep.fa" -otus "${otufolder}/otus.fa" -minsize 2

# Global usearch
./usearch -usearch_global "${otufolder}/merged.fa" -db "${otufolder}/otus.fa" -strand plus -id 0.97 -otutabout "${otufolder}/otus.txt"

# OTU renaming
counter=0
mv "${otufolder}/otus.fa" "${otufolder}/otus_r.fa"
touch "${otufolder}/otus.fa"
while IFS='' read -r line || [[ -n "$line" ]]; do
    if [[ $line == *">"* ]]; then
	    counter=$((counter+1))
	    echo "Text read from file: $line"
	    firstp=$(echo $line | grep -P '^>\d{3}.' -o)
	    secondp=$(echo $line | grep -P ';.+' -o)
	    labelname=$(echo $line | grep -P '(?<=>\d{3}\.)[^;]+' -o)
	    echo "${firstp}otu${counter}${secondp}" >> "${otufolder}/otus.fa"
	    sed -ie "s/${labelname}/otu${counter}/g" "${otufolder}/otus.txt"
    else
	echo $line >> "${otufolder}/otus.fa"
    fi
done < "${otufolder}/otus_r.fa"

# OTU table sorting
python sortotu.py "${otufolder}/otus.txt"
rm "${otufolder}/otus.txt"
mv "${otufolder}/sotus.txt" "${otufolder}/otus.txt"

# Output -- "${otufolder}/otus1.fa" and "${otufolder}/otus.txt"
echo "${otufolder}"