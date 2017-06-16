#Environment settings
RED='\033[0;31m'
NC='\033[0m' # No Color
GREEN='\e[32m'

#read fname
ffolder=$1
primerseq=$2
matchop=$3
taxalg=$4
rdpdb=$5
conflevel=$6
trunlen=$7

# activate qiime
cd ../script
source "/home/qiime/anaconda2/bin/activate" qiime1
echo -e "${RED}activate succeeded${NC}"

# taxonomy assign
if [[ $taxalg == *"RDP"* ]]; then
	export RDP_JAR_PATH="/home/qiime/app/rdp_classifier_2.2/rdp_classifier-2.2.jar"
	
	# if [[ $rdpdb == *"greengenes"* ]]; then
	# 	# tax assign with greengenes db
	# 	assign_taxonomy.py -i "${ffolder}/otus.fa" \
	# 	 -t "./gg_otus_4feb2011/taxonomies/greengenes_tax.txt" \
	# 	 -r "./gg_otus_4feb2011/rep_set/gg_97_otus_4feb2011.fasta" \
	# 	 -c "${conflevel}" \
	# 	 -o "${ffolder}/tax_output" -m rdp	

	if [[ $rdpdb == *"greengenes"* ]]; then
		# tax assign with greengenes db
		assign_taxonomy.py -i "${ffolder}/otus.fa" \
		 -t "./gg_otus_may2013/taxonomy/97_otu_taxonomy.txt" \
		 -r "./gg_otus_may2013/rep_set/97_otus.fasta" \
		 -c "${conflevel}" \
		 -o "${ffolder}/tax_output" -m rdp

	elif [[ $rdpdb == *"silva"* ]]; then
		# tax assign with silva db
		assign_taxonomy.py -i "${ffolder}/otus.fa" \
		 -t "./SILVA_128_QIIME_release/taxonomy/16S_only/97/consensus_taxonomy_7_levels.txt" \
		 -r "./SILVA_128_QIIME_release/rep_set/rep_set_16S_only/97/97_otus_16S.fasta" \
		 -c "${conflevel}" \
		 -o "${ffolder}/tax_output" -m rdp \
		 --rdp_max_memory 16000

	elif [[ $rdpdb == *"unite"* ]]; then
			#statements
			echo "ERROR: Unimplemented"
	else
		# Error
		echo "ERROR: RDP method is not specified"
	fi

elif [[ $taxalg == *"BLAST"* ]]; then
	# tax assign with BLAST
	parallel_assign_taxonomy_blast.py -i "${ffolder}/otus.fa" \
	 -o "${ffolder}/tax_output"

elif [[ $taxalg == *"UCLUST"* ]]; then
	# tax assign with BLAST
	assign_taxonomy.py -i "${ffolder}/otus.fa" \
	 -o "${ffolder}/tax_output" -m uclust

else
	echo "ERROR: tax assign algorithm is not specified"
fi

# run count algorithm and generate count file
python count.py "${ffolder}/otus.txt" "${ffolder}/tax_output/otus_tax_assignments.txt"